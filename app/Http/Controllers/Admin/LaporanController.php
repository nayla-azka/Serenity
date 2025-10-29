<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\LaporanSelesaiDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;
use Carbon\Carbon;

class LaporanController extends AdminBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(LaporanSelesaiDataTable $laporanSelesaiDataTable)
    {
        $userTimezone = session('timezone', 'UTC');

        $laporanDiproses = Report::with('user')
            ->whereIn('status', ['Menunggu', 'Diproses'])
            ->orderByRaw("FIELD(status, 'Menunggu', 'Diproses')")
            ->latest()
            ->get()
            ->map(function ($laporan) use ($userTimezone) {
                $laporan->created_at_tz = Carbon::parse($laporan->created_at, 'UTC')
                    ->setTimezone($userTimezone)
                    ->format('d M Y H:i');

                $laporan->date_tz = Carbon::parse($laporan->date, 'UTC')
                    ->setTimezone($userTimezone)
                    ->format('d M Y');
                
                // Add reporter name handling
                $laporan->reporter_name = $laporan->is_anonymous 
                    ? 'Anonim' 
                    : ($laporan->user->name ?? 'Anonim');

                return $laporan;
            });

        return $laporanSelesaiDataTable->render('admin.laporan.index', compact('laporanDiproses')); 
    }

    /**
     * Get detailed information for a specific report
     */
    public function getDetails($id)
    {
        try {
            $userTimezone = session('timezone', 'UTC');
            
            $laporan = Report::with('user')->findOrFail($id);
            
            // Format dates in user's timezone
            $createdAt = Carbon::parse($laporan->created_at, 'UTC')
                ->setTimezone($userTimezone)
                ->format('d M Y H:i');
            
            $dateFormatted = Carbon::parse($laporan->date, 'UTC')
                ->setTimezone($userTimezone)
                ->format('d M Y');
            
            $updatedAt = Carbon::parse($laporan->updated_at, 'UTC')
                ->setTimezone($userTimezone)
                ->format('d M Y H:i');
            
            return response()->json([
                'id' => $laporan->id,
                'topic' => $laporan->topic,
                'reporter_name' => $laporan->is_anonymous 
                    ? 'Anonim' 
                    : ($laporan->user->name ?? 'Anonim'),
                'date' => $laporan->date,
                'date_formatted' => $dateFormatted,
                'place' => $laporan->place,
                'chronology' => $laporan->chronology,
                'status' => $laporan->status,
                'created_at' => $laporan->created_at,
                'created_at_formatted' => $createdAt,
                'updated_at' => $laporan->updated_at,
                'updated_at_formatted' => $updatedAt,
                'is_anonymous' => $laporan->is_anonymous
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching report details: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch report details'
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        return $this->tryCatchJsonResponse(function () use ($request) {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                abort(400, 'Tidak ada laporan yang dipilih');
            }

            Report::whereIn('id', $ids)->forceDelete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Laporan berhasil dihapus!',
            ]);
        }, 'Gagal menghapus laporan.');
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:Menunggu,Diproses,Selesai,Ditolak'
            ]);

            DB::beginTransaction();

            $laporan = Report::with('user')->findOrFail($id);
            $oldStatus = $laporan->status;
            
            // Update status
            $laporan->status = $request->status;
            $laporan->save();

            // Only send notification if status actually changed and user exists
            if ($oldStatus !== $request->status && $laporan->user) {
                try {
                    $this->notifyReporter($laporan);
                } catch (\Exception $e) {
                    Log::error('Notification error in updateStatus: ' . $e->getMessage(), [
                        'report_id' => $laporan->id,
                        'status' => $request->status
                    ]);
                    // Don't fail the main operation if notifications fail
                }
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Status berhasil diperbarui!',
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
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui status: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function restore(Request $request)
    {
        return $this->tryCatchJsonResponse(function () use ($request) {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                abort(400, 'Tidak ada laporan yang dipilih');
            }

            // Get reports before updating to send notifications
            $reports = Report::with('user')->whereIn('id', $ids)->get();

            Report::whereIn('id', $ids)->update(['status' => 'Diproses']);

            // Send notifications for restored reports
            foreach ($reports as $laporan) {
                if ($laporan->user && $laporan->status !== 'Diproses') {
                    try {
                        // Update the status for notification
                        $laporan->status = 'Diproses';
                        $this->notifyReporter($laporan);
                    } catch (\Exception $e) {
                        Log::error('Notification error in restore: ' . $e->getMessage(), [
                            'report_id' => $laporan->id
                        ]);
                        // Continue with other reports even if one fails
                    }
                }
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Laporan berhasil dikembalikan!',
            ]);
        }, 'Gagal mengembalikan laporan');
    }

    private function notifyReporter($laporan)
    {
        try {
            // Always check if we have a sender_id (even for anonymous reports)
            if (!$laporan->sender_id) {
                Log::warning('No sender_id found for report: ' . $laporan->id);
                return;
            }

            if (!$laporan->user) {
                Log::warning('Reporter user not found for report: ' . $laporan->id);
                return;
            }

            $statusText = [
                'Menunggu' => 'sedang menunggu peninjauan',
                'Diproses' => 'sedang diproses oleh tim kami',
                'Selesai' => 'telah selesai ditangani',
                'Ditolak' => 'ditolak setelah peninjauan'
            ];

            $message = "Status laporan Anda '{$laporan->topic}' telah {$statusText[$laporan->status]}. ";
            
            if ($laporan->status === 'Selesai') {
                $message .= "Terima kasih atas laporan Anda, masalah telah diselesaikan.";
            } elseif ($laporan->status === 'Ditolak') {
                $message .= "Setelah peninjauan, laporan tidak memenuhi kriteria untuk ditindaklanjuti.";
            } elseif ($laporan->status === 'Diproses') {
                $message .= "Tim kami sedang menangani laporan Anda.";
            }

            NotificationService::send(
                $laporan->sender_id, // Always notify the actual sender
                $message,
                'report_status_update',
                [
                    'report_id' => $laporan->id,
                    'status' => $laporan->status,
                    'topic' => $laporan->topic,
                    'date' => $laporan->date,
                    'url' => null
                ]
            );

            Log::info('Reporter notification sent successfully', [
                'report_id' => $laporan->id,
                'reporter_id' => $laporan->sender_id,
                'is_anonymous' => $laporan->is_anonymous
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending reporter notification: ' . $e->getMessage(), [
                'report_id' => $laporan->id,
                'reporter_id' => $laporan->sender_id ?? 'unknown'
            ]);
            throw $e;
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}