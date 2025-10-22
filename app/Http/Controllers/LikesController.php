<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Artikel;
use App\Services\NotificationService;

class LikesController extends Controller
{
    public function toggle(Request $request)
    {
        try {
            $request->validate([
                'target_type' => 'required|in:article,comment',
                'target_id'   => 'required|integer',
            ]);

            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Silakan login terlebih dahulu.'
                ], 401);
            }

            // Check if user already liked this target
            $like = Like::where('user_id', $user->id)
                ->where('target_type', $request->target_type)
                ->where('target_id', $request->target_id)
                ->first();

            if ($like) {
                // Unlike
                $like->delete();
                $liked = false;
            } else {
                // Like
                Like::create([
                    'user_id'     => $user->id,
                    'target_type' => $request->target_type,
                    'target_id'   => $request->target_id,
                ]);
                $liked = true;

                // Send notification only on like
                if ($request->target_type === 'article') {
                    $this->sendArticleLikeNotification($user, $request->target_id);
                } else {
                    $this->sendCommentLikeNotification($user, $request->target_id);
                }
            }

            // Count total likes
            $totalLikes = Like::where('target_type', $request->target_type)
                ->where('target_id', $request->target_id)
                ->count();

            return response()->json([
                'status'      => 'ok',
                'liked'       => $liked,
                'total_likes' => $totalLikes,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak valid.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Like toggle error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'target_type' => $request->target_type ?? null,
                'target_id' => $request->target_id ?? null
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem.',
            ], 500);
        }
    }

    private function sendArticleLikeNotification($liker, $articleId)
    {
        try {
            $article = Artikel::with('author')->find($articleId);

            if ($article && $article->author_id && $article->author_id !== $liker->id) {
                NotificationService::send(
                    $article->author_id,
                    "{$liker->name} menyukai artikel Anda: {$article->title}.",
                    'like',
                    [
                        'url' => route('public.artikel_show', $article->article_id)
                    ]
                );
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send article like notification: ' . $e->getMessage());
        }
    }

    private function sendCommentLikeNotification($liker, $commentId)
    {
        try {
            $comment = Comment::with('article')->find($commentId);

            if ($comment && $comment->user_id && $comment->user_id !== $liker->id) {
                $url = $comment->article 
                    ? route('public.artikel_show', $comment->article_id) . '#comment-' . $commentId
                    : '#';

                NotificationService::send(
                    $comment->user_id,
                    "{$liker->name} menyukai komentar Anda.",
                    'like',
                    ['url' => $url]
                );
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send comment like notification: ' . $e->getMessage());
        }
    }
}