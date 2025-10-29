<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CommentReport;
use App\Models\Comment;
use App\Services\NotificationService;

class CommentReportController extends Controller
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'comment_id' => 'required|exists:comments,comment_id',
        'reason' => 'required|string|max:500',
    ]);

    // Check if user already reported this comment
    $existingReport = CommentReport::where('comment_id', $validated['comment_id'])
        ->where('reported_by', Auth::id())
        ->first();

    if ($existingReport) {
        return response()->json([
            'status' => 'error',
            'message' => 'Anda sudah melaporkan komentar ini sebelumnya.'
        ], 422);
    }

    // Get the comment with user and article info AFTER validation
    $comment = Comment::with(['user', 'article'])->find($validated['comment_id']);
    
    if (!$comment) {
        return response()->json([
            'status' => 'error',
            'message' => 'Komentar tidak ditemukan.'
        ], 404);
    }

    // Create the report
    $report = CommentReport::create([
        'comment_id' => $validated['comment_id'],
        'reported_by' => Auth::id(),
        'reason' => $validated['reason'],
        'status' => 'Pending'
    ]);

    // Get the article
    $article = $comment->article;
    
    if ($comment->user && $comment->user->id !== Auth::id()) {
        // Notify the comment owner
        NotificationService::send(
            $comment->user->id,
            'Komentar Anda dilaporkan oleh pengguna lain dan sedang dalam peninjauan. Tim moderasi akan meninjau laporan ini.',
            'comment_reported',
            [
                'report_id' => $report->report_id,
                'comment_id' => $comment->comment_id,
                'comment_text' => substr($comment->comment_text, 0, 100) . (strlen($comment->comment_text) > 100 ? '...' : ''),
                'url' => $article ? route('public.artikel_show', $article->article_id) . '#comment-' . $comment->comment_id : '#'
            ]
        );
    }

    // Notify admins about new report
    NotificationService::sendToAdmin(
        'Laporan komentar baru: "' . substr($validated['reason'], 0, 50) . (strlen($validated['reason']) > 50 ? '...' : '') . '" - Perlu ditinjau segera.',
        'report_new',
        [
            'report_id' => $report->report_id,
            'comment_id' => $comment->comment_id,
            'reporter_name' => Auth::user()->name,
            'url' => route('admin.report.index', ['highlight' => $report->report_id])
        ]
    );

    return response()->json([
        'status' => 'success',
        'message' => 'Laporan berhasil dikirim. Tim moderasi akan meninjau laporan Anda.'
    ]);
}
}

