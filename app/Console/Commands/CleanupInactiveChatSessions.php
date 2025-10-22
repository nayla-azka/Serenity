<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ChatSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanupInactiveChatSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:cleanup 
                            {--dry-run : Run in dry-run mode without making changes}
                            {--force : Skip confirmation prompts}
                            {--days=30 : Number of days for auto-cleanup threshold}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up inactive chat sessions that have been ended for more than 30 days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $isForced = $this->option('force');
        $cleanupDays = (int) $this->option('days');

        $this->info("Chat Session Cleanup Started");
        $this->info("Cleanup threshold: {$cleanupDays} days");
        $this->info("Mode: " . ($isDryRun ? 'DRY RUN' : 'LIVE'));

        try {
            // Find sessions eligible for auto-cleanup
            $eligibleSessions = ChatSession::where('is_active', false)
                ->where('ended_at', '<', Carbon::now()->subDays($cleanupDays))
                ->where(function($query) {
                    $query->where('deleted_by_student', false)
                          ->orWhere('deleted_by_counselor', false);
                })
                ->with(['student.user', 'counselor'])
                ->get();

            // Find sessions ready for permanent deletion (both parties deleted)
            $readyForDeletion = ChatSession::readyForPermanentDeletion()
                ->with(['student.user', 'counselor'])
                ->get();

            $this->info("\nFound {$eligibleSessions->count()} sessions eligible for auto-cleanup");
            $this->info("Found {$readyForDeletion->count()} sessions ready for permanent deletion");

            if ($eligibleSessions->isEmpty() && $readyForDeletion->isEmpty()) {
                $this->info("No sessions require cleanup at this time.");
                return 0;
            }

            // Display sessions to be cleaned up
            if ($eligibleSessions->isNotEmpty()) {
                $this->warn("\nSessions to be auto-cleaned (force deleted after {$cleanupDays} days):");
                $this->displaySessionTable($eligibleSessions);
            }

            if ($readyForDeletion->isNotEmpty()) {
                $this->warn("\nSessions to be permanently deleted (both parties already deleted):");
                $this->displaySessionTable($readyForDeletion);
            }

            // Confirmation prompt
            if (!$isDryRun && !$isForced) {
                $totalSessions = $eligibleSessions->count() + $readyForDeletion->count();
                if (!$this->confirm("Are you sure you want to permanently delete {$totalSessions} sessions?")) {
                    $this->info("Cleanup cancelled by user.");
                    return 0;
                }
            }

            $cleanupCount = 0;

            // Process auto-cleanup for eligible sessions
            if ($eligibleSessions->isNotEmpty()) {
                $this->info("\nProcessing auto-cleanup for {$eligibleSessions->count()} sessions...");
                
                foreach ($eligibleSessions as $session) {
                    try {
                        if (!$isDryRun) {
                            // Force mark as deleted by both parties
                            $session->update([
                                'deleted_by_student' => true,
                                'deleted_by_counselor' => true,
                                'student_deleted_at' => Carbon::now(),
                                'counselor_deleted_at' => Carbon::now()
                            ]);
                            
                            // Get message count for logging
                            $messageCount = $session->messages()->count();
                            
                            // Permanently delete
                            $session->permanentlyDelete();
                            
                            Log::info("Auto-cleaned session {$session->id_session} with {$messageCount} messages");
                        }
                        
                        $studentName = $session->student->user->name ?? 'Unknown';
                        $counselorName = $session->counselor->counselor_name ?? 'Unknown';
                        
                        $this->line("✓ Session #{$session->id_session}: {$studentName} ↔ {$counselorName}");
                        $cleanupCount++;
                        
                    } catch (\Exception $e) {
                        $this->error("✗ Failed to cleanup session #{$session->id_session}: " . $e->getMessage());
                        Log::error("Failed to auto-clean session {$session->id_session}: " . $e->getMessage());
                    }
                }
            }

            // Process permanent deletion for ready sessions
            if ($readyForDeletion->isNotEmpty()) {
                $this->info("\nProcessing permanent deletion for {$readyForDeletion->count()} sessions...");
                
                foreach ($readyForDeletion as $session) {
                    try {
                        if (!$isDryRun) {
                            $messageCount = $session->messages()->count();
                            $session->permanentlyDelete();
                            Log::info("Permanently deleted session {$session->id_session} with {$messageCount} messages");
                        }
                        
                        $studentName = $session->student->user->name ?? 'Unknown';
                        $counselorName = $session->counselor->counselor_name ?? 'Unknown';
                        
                        $this->line("✓ Session #{$session->id_session}: {$studentName} ↔ {$counselorName}");
                        $cleanupCount++;
                        
                    } catch (\Exception $e) {
                        $this->error("✗ Failed to delete session #{$session->id_session}: " . $e->getMessage());
                        Log::error("Failed to permanently delete session {$session->id_session}: " . $e->getMessage());
                    }
                }
            }

            // Summary
            $this->info("\n" . str_repeat('=', 50));
            if ($isDryRun) {
                $this->info("DRY RUN COMPLETE");
                $this->info("Would have cleaned up {$cleanupCount} sessions");
            } else {
                $this->info("CLEANUP COMPLETE");
                $this->info("Successfully cleaned up {$cleanupCount} sessions");
            }
            $this->info("Timestamp: " . Carbon::now()->format('Y-m-d H:i:s'));

            return 0;

        } catch (\Exception $e) {
            $this->error("Cleanup failed with error: " . $e->getMessage());
            Log::error("Chat cleanup command failed: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Display a table of sessions
     */
    private function displaySessionTable($sessions)
    {
        $tableData = [];
        
        foreach ($sessions as $session) {
            $studentName = $session->student->user->name ?? 'Unknown Student';
            $counselorName = $session->counselor->counselor_name ?? 'Unknown Counselor';
            $messageCount = $session->messages()->count();
            $endedDaysAgo = $session->ended_at ? $session->ended_at->diffInDays(Carbon::now()) : 'N/A';
            
            $deletionStatus = [];
            if ($session->deleted_by_student) $deletionStatus[] = 'Student';
            if ($session->deleted_by_counselor) $deletionStatus[] = 'Counselor';
            $deletedBy = empty($deletionStatus) ? 'None' : implode(', ', $deletionStatus);
            
            $tableData[] = [
                'ID' => $session->id_session,
                'Student' => $studentName,
                'Counselor' => $counselorName,
                'Messages' => $messageCount,
                'Ended Days Ago' => $endedDaysAgo,
                'Deleted By' => $deletedBy,
                'Topic' => \Str::limit($session->topic, 20)
            ];
        }

        $this->table([
            'ID', 'Student', 'Counselor', 'Messages', 'Ended Days Ago', 'Deleted By', 'Topic'
        ], $tableData);
    }
}