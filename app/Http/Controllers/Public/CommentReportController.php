<?php

namespace App\Http\Controllers\Public;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CommentReport;
use App\Models\User;
use App\Notifications\ReportNotification;

class CommentReportController extends PublicBaseController
{
    public function store(Request $request)
    {
        $request->validate([
            'reason' => 'required|string',
            'comment_id' => 'nullable|exists:comments,comment_id',
            'reply_id' => 'nullable|exists:comment_replies,reply_id',
        ]);

        // Tentukan target_type otomatis
        if ($request->filled('comment_id')) {
            $targetType = 'comment';
        } elseif ($request->filled('reply_id')) {
            $targetType = 'reply';
        } else {
            return back()->withErrors(['target' => 'Target laporan tidak valid']);
        }

        CommentReport::create([
            'target_type' => $targetType,
            'comment_id'  => $request->comment_id,
            'reply_id'    => $request->reply_id,
            'reported_by' => Auth::id(),
            'reason'      => $request->reason,
            'status'      => 'pending',
        ]);

        // Kirim notifikasi ke admin
        // $admins = User::where('role', 'admin')->get();
        // foreach ($admins as $admin) {
        //     $admin->notify(new ReportNotification($report));
        // }

        return back()->with('success', 'Laporan berhasil dikirim.');
    }

}
