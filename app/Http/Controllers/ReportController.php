<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

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

        // Always store the sender_id, but flag as anonymous if checkbox is checked
        $report = Report::create([
            'sender_id'  => Auth::id(), // Always store the actual sender
            'is_anonymous' => $request->has('sembunyikan_identitas'), // Flag for anonymity
            'topic'      => $validated['topic'],
            'date'       => $validated['date'],
            'place'      => $validated['place'],
            'chronology' => $validated['chronology'],
            'status'     => 'Menunggu',
        ]);

        // 🔔 Notify counselors
        try {
            $konselors = User::konselors();
            $reporterName = $report->is_anonymous ? 'Anonim' : Auth::user()->name;
            
            foreach ($konselors as $konselor) {
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
                        'reporter'   => $reporterName,
                        'url' => route('admin.laporan.index', ['highlight' => $report->id]),
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Error sending counselor notifications: ' . $e->getMessage(), [
                'report_id' => $report->id
            ]);
            // Don't fail the report submission if notifications fail
        }

        return redirect()->route('public.lapor')->with('success', 'Laporan berhasil dikirim!');
    }
}