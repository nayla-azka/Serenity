<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ReportDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CommentReport;
use App\Models\Comment;
use App\Notifications\ReporterNotification;
use App\Notifications\ReportedUserNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends AdminBaseController
{
    public function index(ReportDataTable $dataTable)
    {
         $counts = [
            'all' => CommentReport::count(),
            'Pending' => CommentReport::where('status', 'Pending')->count(),
            'Diterima' => CommentReport::where('status', 'Diterima')->count(),
            'Ditolak' => CommentReport::where('status', 'Ditolak')->count(),
        ];

        $highlightReport = request()->get('highlight');

        return $dataTable->render('admin.report.index', compact('counts', 'highlightReport'));
    }

    public function show($id)
    {
        $report = CommentReport::with(['comment.user', 'reporter'])->findOrFail($id);

        // Mark notification as read if it exists
        $notification = auth()->user()->unreadNotifications()
            ->where('data->report_id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return view('admin.report.index', compact('report'));
    }

    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:comment_reports,report_id',
                'status' => 'required|in:Pending,Diterima,Ditolak',
                'admin_notes' => 'nullable|string|max:500'
            ]);

            DB::beginTransaction();

            $report = CommentReport::with(['comment.user', 'reporter'])->findOrFail($request->id);
            $oldStatus = $report->status;
            
            // Update report
            $report->status = $request->status;

            if ($request->filled('admin_notes')) {
                $report->admin_notes = $request->admin_notes;
            }

            if ($request->status === 'Pending') {
                // Reset review info
                $report->reviewed_by = null;
                $report->reviewed_at = null;
            } else {
                // Set review info
                $report->reviewed_by = auth()->id();
                $report->reviewed_at = now();
            }

            $report->save();

            // Only proceed with actions if status actually changed
            if ($oldStatus !== $request->status) {
                
                // If report is accepted, remove the target content and all nested replies
                if ($request->status === 'Diterima' && $report->comment && !$report->comment->is_removed) {
                    $this->removeCommentAndReplies($report->comment);
                    $this->updateArticleCommentCount($report->comment);
                }
                
                // If changing FROM Diterima TO Pending/Ditolak, restore the comment
                if ($oldStatus === 'Diterima' && in_array($request->status, ['Pending', 'Ditolak']) && $report->comment) {
                    $this->restoreCommentAndReplies($report->comment);
                    $this->updateArticleCommentCount($report->comment);
                }

                // Send notifications (but don't let them fail the main operation)
                try {
                    // Notify the reporter about the status update
                    if ($report->reporter) {
                        $this->notifyReporter($report);
                    }

                    // Notify the comment owner about the status update
                    $targetUser = $report->targetUser();
                    if ($targetUser && $targetUser->id !== $report->reported_by) {
                        $this->notifyCommentOwner($report);
                    }
                } catch (\Exception $e) {
                    Log::error('Notification error in updateStatus: ' . $e->getMessage(), [
                        'report_id' => $report->report_id,
                        'status' => $request->status
                    ]);
                    // Don't fail the main operation if notifications fail
                }
            }

            DB::commit();

            // return updated counts
            $counts = [
                'all' => CommentReport::count(),
                'Pending' => CommentReport::where('status', 'Pending')->count(),
                'Diterima' => CommentReport::where('status', 'Diterima')->count(),
                'Ditolak' => CommentReport::where('status', 'Ditolak')->count(),
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Status laporan berhasil diperbarui!',
                'counts' => $counts
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid: ' . implode(', ', $e->validator->errors()->all())
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in updateStatus: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recursively remove a comment and all its nested replies
     * 
     * @param Comment $comment
     * @return void
     */
    private function removeCommentAndReplies(Comment $comment)
    {
        try {
            // Mark the comment as removed
            $comment->update(['is_removed' => 1]);
            
            Log::info('Comment removed', [
                'comment_id' => $comment->comment_id,
                'has_replies' => $comment->allReplies()->exists()
            ]);

            // Get ALL direct replies to this comment (including already removed ones)
            // Use allReplies() instead of replies() to get the complete tree
            $replies = $comment->allReplies;

            // Recursively remove each reply and their nested replies
            foreach ($replies as $reply) {
                // Only process if not already removed to avoid redundant updates
                if ($reply->is_removed == 0) {
                    $this->removeCommentAndReplies($reply);
                }
            }

            Log::info('Removed comment and all nested replies', [
                'parent_comment_id' => $comment->comment_id,
                'total_replies_processed' => $replies->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error removing comment and replies: ' . $e->getMessage(), [
                'comment_id' => $comment->comment_id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Recursively restore a comment and all its nested replies
     * 
     * @param Comment $comment
     * @return void
     */
    private function restoreCommentAndReplies(Comment $comment)
    {
        try {
            // Mark the comment as active
            $comment->update(['is_removed' => 0]);
            
            Log::info('Comment restored', [
                'comment_id' => $comment->comment_id,
                'has_replies' => $comment->allReplies()->exists()
            ]);

            // Get ALL direct replies to this comment
            $replies = $comment->allReplies;

            // Recursively restore each reply and their nested replies
            foreach ($replies as $reply) {
                // Only process if currently removed
                if ($reply->is_removed == 1) {
                    $this->restoreCommentAndReplies($reply);
                }
            }

            Log::info('Restored comment and all nested replies', [
                'parent_comment_id' => $comment->comment_id,
                'total_replies_processed' => $replies->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error restoring comment and replies: ' . $e->getMessage(), [
                'comment_id' => $comment->comment_id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update the article's comment count after removing/restoring comments
     * 
     * @param Comment $comment
     * @return void
     */
    private function updateArticleCommentCount(Comment $comment)
    {
        try {
            $article = $comment->article;
            
            if ($article) {
                // Count only active comments for this article
                $activeCommentCount = Comment::where('article_id', $article->article_id)
                                            ->where('is_removed', 0)
                                            ->count();
                
                // Update the article's total_comments
                $article->update(['total_comments' => $activeCommentCount]);
                
                Log::info('Updated article comment count', [
                    'article_id' => $article->article_id,
                    'new_count' => $activeCommentCount
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating article comment count: ' . $e->getMessage(), [
                'comment_id' => $comment->comment_id ?? 'unknown'
            ]);
            // Don't throw - this is not critical enough to fail the main operation
        }
    }

    private function notifyReporter($report)
    {
        try {
            if (!$report->reporter) {
                Log::warning('Reporter not found for report: ' . $report->report_id);
                return;
            }

            $statusText = [
                'Pending' => 'sedang ditinjau',
                'Diterima' => 'diterima dan tindakan telah diambil',
                'Ditolak' => 'ditolak setelah peninjauan'
            ];

            $message = "Laporan komentar Anda telah {$statusText[$report->status]}. ";
            
            if ($report->status === 'Diterima') {
                $message .= "Terima kasih atas laporan Anda, komentar yang dilaporkan telah dihapus.";
            } elseif ($report->status === 'Ditolak') {
                $message .= "Setelah peninjauan, komentar tersebut tidak melanggar aturan komunitas.";
            }

            // Add admin notes if present
            if (!empty($report->admin_notes)) {
                $message .= "\n\nCatatan Admin: " . $report->admin_notes;
            }

            NotificationService::send(
                $report->reported_by,
                $message,
                'report_status_update',
                [
                    'report_id' => $report->report_id,
                    'status' => $report->status,
                    'comment_id' => $report->comment_id,
                    'admin_notes' => $report->admin_notes
                ]
            );

            Log::info('Reporter notification sent successfully', [
                'report_id' => $report->report_id,
                'reporter_id' => $report->reported_by
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending reporter notification: ' . $e->getMessage(), [
                'report_id' => $report->report_id,
                'reporter_id' => $report->reported_by ?? 'unknown'
            ]);
            throw $e;
        }
    }

    private function notifyCommentOwner($report)
    {
        try {
            $commentOwner = $report->targetUser();
            if (!$commentOwner) {
                Log::warning('Comment owner not found for report: ' . $report->report_id);
                return;
            }

            $message = '';
            $type = 'report_result';

            if ($report->status === 'Diterima') {
                $message = "Komentar Anda telah dihapus karena melanggar aturan komunitas berdasarkan laporan dari pengguna lain. Silakan baca panduan komunitas untuk menghindari hal serupa di masa depan.";
            } elseif ($report->status === 'Ditolak') {
                $message = "Laporan terhadap komentar Anda telah ditinjau dan Ditolak. Komentar Anda tidak melanggar aturan komunitas. Terima kasih atas kesabaran Anda.";
            }

            // Add admin notes if present
            if (!empty($report->admin_notes)) {
                $message .= "\n\nCatatan Admin: " . $report->admin_notes;
            }

            if ($message) {
                NotificationService::send(
                    $commentOwner->id,
                    $message,
                    $type,
                    [
                        'report_id' => $report->report_id,
                        'status' => $report->status,
                        'comment_id' => $report->comment_id,
                        'comment_text' => substr($report->comment->comment_text ?? '', 0, 100),
                        'admin_notes' => $report->admin_notes
                    ]
                );

                Log::info('Comment owner notification sent successfully', [
                    'report_id' => $report->report_id,
                    'owner_id' => $commentOwner->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error sending comment owner notification: ' . $e->getMessage(), [
                'report_id' => $report->report_id,
                'owner_id' => $commentOwner->id ?? 'unknown'
            ]);
            throw $e;
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:comment_reports,report_id'
            ]);

            $ids = $request->ids;
            if (!empty($ids)) {
                DB::beginTransaction();

                 // Get reports with their comments
                    $reports = CommentReport::with('comment')->whereIn('report_id', $ids)->get();
                    
                    // ✅ Count reports BEFORE deleting anything
                    $deletedCount = $reports->count();
                    
                    $commentsDeleted = 0;
                    foreach ($reports as $report) {
                        // If comment exists and is removed, permanently delete it and all replies
                        if ($report->comment && $report->comment->is_removed == 1) {
                            $commentsDeleted += $this->permanentlyDeleteCommentAndReplies($report->comment);
                        } else {
                            // If comment doesn't need permanent deletion, just delete the report
                            $report->delete();
                        }
                    }

                DB::commit();

                // get fresh counts after deletion
                $counts = [
                    'all' => CommentReport::count(),
                    'Pending' => CommentReport::where('status', 'Pending')->count(),
                    'Diterima' => CommentReport::where('status', 'Diterima')->count(),
                    'Ditolak' => CommentReport::where('status', 'Ditolak')->count(),
                ];
                
                $message = "Berhasil menghapus {$deletedCount} laporan!";
                if ($commentsDeleted > 0) {
                    $message .= " ({$commentsDeleted} komentar dihapus permanen dari database)";
                }
                
                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'counts' => $counts,
                    'comments_deleted' => $commentsDeleted
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada laporan yang dipilih'
            ], 400);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bulkDelete: ' . $e->getMessage(), [
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete a comment and all its nested replies from database
     * 
     * @param Comment $comment
     * @return int Number of comments deleted
     */
    private function permanentlyDeleteCommentAndReplies(Comment $comment)
    {
        try {
            $deletedCount = 1; // Count the current comment
            
            Log::info('Permanently deleting comment', [
                'comment_id' => $comment->comment_id,
                'has_replies' => $comment->allReplies()->exists()
            ]);

            // Get ALL direct replies to this comment
            $replies = $comment->allReplies;

            // Recursively delete each reply and their nested replies
            foreach ($replies as $reply) {
                $deletedCount += $this->permanentlyDeleteCommentAndReplies($reply);
            }

            // Delete all likes associated with this comment
            DB::table('likes')
                ->where('target_type', 'comment')
                ->where('target_id', $comment->comment_id)
                ->delete();

            // Delete all reports associated with this comment
            DB::table('comment_reports')
                ->where('comment_id', $comment->comment_id)
                ->delete();

            // Finally, delete the comment itself
            $comment->delete();

            Log::info('Permanently deleted comment and all nested replies', [
                'parent_comment_id' => $comment->comment_id,
                'total_deleted' => $deletedCount
            ]);

            return $deletedCount;

        } catch (\Exception $e) {
            Log::error('Error permanently deleting comment and replies: ' . $e->getMessage(), [
                'comment_id' => $comment->comment_id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}