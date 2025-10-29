<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class CommentRepliesController extends Controller
{
    public function loadReplies(Request $request, $comment_id)
    {
        try {
            $page = $request->get('page', 1);
            $level = $request->get('level', 1);
            $perPage = 5;
            $offset = ($page - 1) * $perPage;

            // Validate that the parent comment exists
            $parentComment = Comment::find($comment_id);
            if (!$parentComment) {
                return response()->json([
                    'error' => 'Parent comment not found'
                ], 404);
            }

            $replies = Comment::with(['user.siswa', 'user.counselorProfile'])
                ->where('parent_id', $comment_id)
                ->where('is_removed', 0)
                ->latest()
                ->offset($offset)
                ->limit($perPage)
                ->get();

            // Add counts manually for each reply
            foreach ($replies as $reply) {
                $reply->likes_count = $reply->likes()->count();
                $reply->replies_count = $reply->activeReplies()->count(); // Fixed: use activeReplies

                // Check liked status for each reply
                if (Auth::check()) {
                    $reply->liked_by_user = DB::table('likes')
                        ->where('user_id', Auth::id())
                        ->where('target_type', 'comment')
                        ->where('target_id', $reply->comment_id)
                        ->exists();
                } else {
                    $reply->liked_by_user = false;
                }
            }

            // Get total replies count
            $totalReplies = Comment::where('parent_id', $comment_id)
                ->where('is_removed', 0)
                ->count();

            $loadedSoFar = $offset + $replies->count();
            $hasMore = $totalReplies > $loadedSoFar;
            $remainingCount = $totalReplies - $loadedSoFar;

            $html = '';
            foreach ($replies as $comment) {
                $html .= view('public.partials.comment', compact('comment', 'level'))->render();
            }

            return response()->json([
                'html' => $html,
                'has_more' => $hasMore,
                'remaining_count' => $remainingCount,
                'next_page' => $page + 1,
                'total_replies' => $totalReplies,
                'loaded_count' => $replies->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in loadReplies: ' . $e->getMessage(), [
                'comment_id' => $comment_id,
                'page' => $request->get('page', 1),
                'level' => $request->get('level', 1),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to load replies',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'comment_id' => 'required|exists:comments,comment_id',
                'reply_text' => 'required|string|max:1000'
            ]);

            $parentComment = Comment::findOrFail($request->comment_id);

            // Create the reply
            $comment = Comment::create([
                'article_id' => $parentComment->article_id,
                'user_id' => Auth::id(),
                'comment_text' => $request->reply_text,
                'parent_id' => $request->comment_id
            ]);

            if (!$comment) {
                throw new \Exception('Failed to create comment in database');
            }

            // Load relationships safely
            try {
                $comment = $comment->fresh(['user.siswa', 'user.counselorProfile']);
                $comment->likes_count = $comment->likes()->count();
                $comment->replies_count = $comment->activeReplies()->count(); // Fixed: use activeReplies
                $comment->liked_by_user = false;
            } catch (\Exception $e) {
                \Log::error('Failed to load comment relationships: ' . $e->getMessage());
                throw new \Exception('Failed to load comment data');
            }

            // Calculate nesting level safely
            $level = 1;
            try {
                $level = $this->calculateNestingLevel($parentComment) + 1;
                if ($level > 5) {
                    $level = 5; // Cap at maximum nesting level
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to calculate nesting level, using default: ' . $e->getMessage());
            }

            // Generate HTML response
            $html = '';
            try {
                $html = view('public.partials.comment', compact('comment', 'level'))->render();
            } catch (\Exception $e) {
                \Log::error('Failed to render comment view: ' . $e->getMessage());
                throw new \Exception('Failed to generate comment display');
            }

            // Send notifications (non-blocking)
            try {
                $this->sendReplyNotifications($comment, $parentComment);
            } catch (\Exception $e) {
                \Log::warning('Failed to send reply notifications: ' . $e->getMessage());
                // Don't fail the request for notification errors
            }

            // Get updated parent comment reply count
            $parentReplyCount = $parentComment->activeReplies()->count();

            return response()->json([
                'status' => 'success',
                'message' => 'Balasan berhasil ditambahkan!',
                'html' => $html,
                'comment_id' => $comment->comment_id,
                'level' => $level,
                'parent_comment_id' => $parentComment->comment_id,
                'parent_replies_count' => $parentReplyCount
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::info('Reply validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid: ' . implode(', ', $e->validator->errors()->all()),
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::warning('Parent comment not found', ['comment_id' => $request->comment_id]);
            return response()->json([
                'status' => 'error',
                'message' => 'Komentar yang ingin dibalas tidak ditemukan.'
            ], 404);

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error in reply creation', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'request_data' => $request->only(['comment_id', 'reply_text'])
            ]);

            if ($e->getCode() === '23000') { // Foreign key constraint violation
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak dapat membalas komentar ini karena masalah referensi data.'
                ], 400);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan database saat menyimpan balasan.'
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Unexpected error in reply creation', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->only(['comment_id', 'reply_text']),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi dalam beberapa saat.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Send notifications for reply creation
     */
    /**
 * Send notifications for reply creation
 */
private function sendReplyNotifications($comment, $parentComment)
{
    try {
        $comment->load('article.author');
        $article = $comment->article;

        if (!$article) {
            \Log::warning('Article not found for comment notification', ['comment_id' => $comment->comment_id]);
            return;
        }

        $currentUserId = Auth::id();
        $currentUserName = Auth::user()->name ?? 'Someone';
        $notifiedUsers = []; // Track who we've notified to avoid duplicates

        \Log::info('Starting reply notifications', [
            'current_user' => $currentUserId,
            'parent_comment_user' => $parentComment->user_id,
            'article_author' => $article->author_id
        ]);

        // 1. Notify the direct parent comment owner
        if ($parentComment->user_id && 
            $parentComment->user_id !== $currentUserId) {
            
            \Log::info('Attempting to notify parent comment owner', [
                'user_id' => $parentComment->user_id
            ]);
            
            try {
                NotificationService::send(
                    $parentComment->user_id,
                    "{$currentUserName} membalas komentar Anda di artikel \"{$article->title}\".",
                    'reply',
                    [
                        'url' => route('public.articles.comment', [
                            'article_id' => $article->article_id,
                            'comment_id' => $comment->comment_id
                        ])
                    ]
                );
                $notifiedUsers[] = $parentComment->user_id;
                \Log::info('Successfully notified parent comment owner');
            } catch (\Exception $e) {
                \Log::error('Failed to notify parent comment owner: ' . $e->getMessage());
            }
        } else {
            \Log::info('Skipping parent notification', [
                'reason' => $parentComment->user_id === $currentUserId ? 'same user' : 'no user_id'
            ]);
        }

        // 2. Walk up the comment chain and notify all ancestors
        $currentAncestor = $parentComment;
        $maxDepth = 5;
        $depth = 0;
        
        while ($currentAncestor->parent_id && $depth < $maxDepth) {
            $depth++;
            $ancestorComment = Comment::find($currentAncestor->parent_id);
            
            if (!$ancestorComment) break;
            
            \Log::info("Checking ancestor at depth {$depth}", [
                'ancestor_user' => $ancestorComment->user_id,
                'already_notified' => in_array($ancestorComment->user_id, $notifiedUsers)
            ]);
            
            if ($ancestorComment->user_id && 
                $ancestorComment->user_id !== $currentUserId &&
                !in_array($ancestorComment->user_id, $notifiedUsers)) {
                
                try {
                    NotificationService::send(
                        $ancestorComment->user_id,
                        "{$currentUserName} membalas diskusi Anda di artikel \"{$article->title}\".",
                        'reply',
                        [
                            'url' => route('public.artikel_show', $article->article_id) . '#comment-' . $comment->comment_id
                        ]
                    );
                    $notifiedUsers[] = $ancestorComment->user_id;
                    \Log::info("Successfully notified ancestor");
                } catch (\Exception $e) {
                    \Log::error('Failed to notify ancestor: ' . $e->getMessage());
                }
            }
            
            $currentAncestor = $ancestorComment;
        }

        // 3. Notify article author (if not already notified)
        if ($article->author_id && 
            $article->author_id !== $currentUserId &&
            !in_array($article->author_id, $notifiedUsers)) {
            
            \Log::info('Attempting to notify article author', [
                'author_id' => $article->author_id
            ]);
            
            try {
                NotificationService::send(
                    $article->author_id,
                    "{$currentUserName} membalas komentar pada artikel Anda: {$article->title}.",
                    'reply',
                    [
                        'url' => route('public.artikel_show', $article->article_id) . '#comment-' . $comment->comment_id
                    ]
                );
                \Log::info('Successfully notified article author');
            } catch (\Exception $e) {
                \Log::error('Failed to notify article author: ' . $e->getMessage());
            }
        } else {
            \Log::info('Skipping article author notification', [
                'reason' => $article->author_id === $currentUserId ? 'same user' : 
                           (in_array($article->author_id, $notifiedUsers) ? 'already notified' : 'no author_id')
            ]);
        }

        \Log::info('Finished reply notifications', [
            'total_notified' => count($notifiedUsers),
            'notified_users' => $notifiedUsers
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in sendReplyNotifications: ' . $e->getMessage());
        throw $e;
    }
}

    public function destroy($reply_id)
    {
        try {
            $reply = Comment::findOrFail($reply_id);

            // Check permissions: owner, admin, or konselor
            if (
                $reply->user_id !== Auth::id() &&
                !in_array(Auth::user()->role ?? '', ['admin', 'konselor'])
            ) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki izin untuk menghapus balasan ini.'
                ], 403);
            }

            // Recursively delete the reply and all its nested replies
            $this->deleteCommentRecursively($reply);

            return response()->json([
                'status' => 'ok',
                'message' => 'Balasan berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in destroy reply: ' . $e->getMessage(), [
                'reply_id' => $reply_id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus balasan.'
            ], 500);
        }
    }

    /**
     * Calculate nesting level of a comment
     */
    private function calculateNestingLevel($comment)
    {
        $level = 0;
        $current = $comment;

        while ($current && $current->parent_id) {
            $level++;
            $current = Comment::find($current->parent_id);
            if (!$current || $level > 10) break; // Safety check to prevent infinite loops
        }

        return $level;
    }

    /**
     * Recursively delete a comment and all its nested replies
     */
    private function deleteCommentRecursively($comment)
    {
        // Get all direct replies first
        $replies = Comment::where('parent_id', $comment->comment_id)->get();

        // Recursively delete each reply
        foreach ($replies as $reply) {
            $this->deleteCommentRecursively($reply);
        }

        // Delete the comment itself
        $comment->delete();
    }
}
