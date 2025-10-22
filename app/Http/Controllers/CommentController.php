<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Artikel;
use App\Services\NotificationService;

class CommentController extends Controller
{
    public function store(Request $request, $article_id)
    {
        $request->validate([
            'comment_text' => 'required|string|max:1000'
        ]);

        $comment = Comment::create([
            'article_id' => $article_id,
            'user_id' => Auth::id(),
            'comment_text' => $request->comment_text,
            'parent_id' => null // This ensures it's a parent comment
        ]);

        // Load the comment with relationships for the response
        $comment->load(['user.siswa', 'user.counselorProfile']);
        $comment->likes_count = $comment->likes()->count();
        $comment->replies_count = $comment->activeReplies()->count(); // Fixed: use activeReplies

        // Check if current user liked this comment
        $comment->liked_by_user = DB::table('likes')
            ->where('user_id', Auth::id())
            ->where('target_type', 'comment')
            ->where('target_id', $comment->comment_id)
            ->exists();

        // Send notification to article author
        $article = Artikel::with('author')->find($article_id);
        if ($article && $article->author_id !== Auth::id()) {
            NotificationService::send(
                $article->author_id,
                Auth::user()->name . " berkomentar pada artikel Anda: {$article->title}.",
                'comment',
                [
                    'url' => route('public.artikel_show', $article->article_id) . '#comment-' . $comment->comment_id
                ]
            );
        }

       // Pass level 0 for main comments
        $level = 0;

        return response()->json([
            'status' => 'success',
            'message' => 'Komentar berhasil ditambahkan!',
            'html' => view('public.partials.comment', compact('comment', 'level'))->render()
        ]);
    }

    public function destroy($comment_id)
    {
       $comment = Comment::findOrFail($comment_id);

        // Check permissions: owner, admin, or konselor
        if (
            $comment->user_id !== Auth::id() &&
            !in_array(Auth::user()->role ?? '', ['admin', 'konselor'])
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki izin untuk menghapus komentar ini.'
            ], 403);
        }

        // If this is a parent comment, also delete all nested replies
        $this->deleteCommentRecursively($comment);

        return response()->json([
            'status' => 'ok',
            'message' => 'Komentar berhasil dihapus.'
        ]);
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

   public function loadMore(Request $request, $article_id)
   {
       $page = $request->get('page', 1);
       $perPage = 10;
       $offset = ($page - 1) * $perPage;

       // Load comments with proper reply counts
       $comments = Comment::active()
           ->parentComments()
           ->with(['user.siswa', 'user.counselorProfile'])
           ->where('article_id', $article_id)
           ->latest()
           ->offset($offset)
           ->limit($perPage)
           ->get();

       // Add counts manually to ensure proper naming
       foreach ($comments as $comment) {
           // Add likes count
           $comment->likes_count = $comment->likes()->count();

           // Add replies count - this is the key fix!
           $comment->replies_count = $comment->activeReplies()->count();

           // Check if current user liked this comment
           if (Auth::check()) {
               $comment->liked_by_user = DB::table('likes')
                   ->where('user_id', Auth::id())
                   ->where('target_type', 'comment')
                   ->where('target_id', $comment->comment_id)
                   ->exists();
           } else {
               $comment->liked_by_user = false;
           }
       }

       $html = '';
       $level = 0;
       foreach ($comments as $comment) {
           $html .= view('public.partials.comment', compact('comment', 'level'))->render();
       }

       // Count total active parent comments
       $totalComments = Comment::active()
           ->parentComments()
           ->where('article_id', $article_id)
           ->count();

       $hasMore = $totalComments > ($offset + $perPage);

       return response()->json([
           'html' => $html,
           'has_more' => $hasMore,
           'count' => $comments->count(),
           'next_page' => $page + 1
       ]);
   }
}
