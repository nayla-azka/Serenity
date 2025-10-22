<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\NotificationService;

class ReportController extends Controller
{
    public function create()
    {
        return view('public.lapor');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'date' => 'required|date',
            'place' => 'required|string|max:255',
            'chronology' => 'required|string',
        ]);

        $report = Report::create([
            'sender_id'  => $request->has('sembunyikan_identitas') ? null : Auth::id(),
            'topic'      => $validated['topic'],
            'date'       => $validated['date'],
            'place'      => $validated['place'],
            'chronology' => $validated['chronology'],
            'status'     => 'Menunggu',
        ]);

        // 🔔 Notify konselors
        foreach (User::konselors() as $konselor) {
            NotificationService::sendToKonselor(
                $konselor->id,
                'Laporan baru diajukan: "' . substr($report->topic, 0, 50) . '"',
                'report_new',
                [
                    'report_id'  => $report->id,
                    'topic'      => $report->topic,
                    'place'      => $report->place,
                    'date'       => $report->date,
                    'chronology' => substr($report->chronology, 0, 100) . (strlen($report->chronology) > 100 ? '...' : ''),
                    'reporter'   => $report->sender_id ? Auth::user()->name : 'Anonim',
                    'url' => route('admin.laporan.index', ['highlight' => $report->id]),
                ]
            );
        }

        return redirect()->route('public.lapor')->with('success', 'Laporan berhasil dikirim!');
    }
}
