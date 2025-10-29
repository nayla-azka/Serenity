<?php

// File: routes/console.php (Laravel 12+ structure)

use Illuminate\Support\Facades\Artisan;

// Additional utility commands for chat management
Artisan::command('chat:stats', function () {
    $this->info('Chat System Statistics');
    $this->info('===================');
    
    $totalSessions = \App\Models\ChatSession::count();
    $activeSessions = \App\Models\ChatSession::where('is_active', true)->count();
    $endedSessions = \App\Models\ChatSession::where('is_active', false)->count();
    $archivedSessions = \App\Models\ChatSession::where('is_archived', true)->count();
    $deletedByStudents = \App\Models\ChatSession::where('deleted_by_student', true)->count();
    $deletedByCounselors = \App\Models\ChatSession::where('deleted_by_counselor', true)->count();
    $readyForDeletion = \App\Models\ChatSession::where('deleted_by_student', true)
        ->where('deleted_by_counselor', true)->count();
    
    $eligibleForCleanup = \App\Models\ChatSession::where('is_active', false)
        ->where('ended_at', '<', \Carbon\Carbon::now()->subDays(30))
        ->where(function($query) {
            $query->where('deleted_by_student', false)
                  ->orWhere('deleted_by_counselor', false);
        })->count();
    
    $totalMessages = \App\Models\ChatMessage::count();
    
    $this->table([
        'Metric', 'Count'
    ], [
        ['Total Sessions', $totalSessions],
        ['Active Sessions', $activeSessions],
        ['Ended Sessions', $endedSessions],
        ['Archived Sessions', $archivedSessions],
        ['Deleted by Students', $deletedByStudents],
        ['Deleted by Counselors', $deletedByCounselors],
        ['Ready for Permanent Deletion', $readyForDeletion],
        ['Eligible for Auto-cleanup', $eligibleForCleanup],
        ['Total Messages', $totalMessages],
    ]);
    
})->purpose('Show chat system statistics');

Artisan::command('chat:force-cleanup {sessionId}', function (int $sessionId) {
    $session = \App\Models\ChatSession::find($sessionId);
    
    if (!$session) {
        $this->error("Session {$sessionId} not found");
        return 1;
    }
     $this->info("Session #{$sessionId} Details:");
    $this->info("Active: " . ($session->is_active ? 'Yes' : 'No'));
    $this->info("Deleted by Student: " . ($session->deleted_by_student ? 'Yes' : 'No'));
    $this->info("Deleted by Counselor: " . ($session->deleted_by_counselor ? 'Yes' : 'No'));
    $this->info("Messages: " . $session->messages()->count());
    
    if (!$this->confirm("Force delete this session permanently?")) {
        $this->info("Cancelled");
        return 0;
    }
    
    try {
        $messageCount = $session->messages()->count();
        
        // Force mark as deleted by both parties
        $session->update([
            'deleted_by_student' => true,
            'deleted_by_counselor' => true,
            'student_deleted_at' => \Carbon\Carbon::now(),
            'counselor_deleted_at' => \Carbon\Carbon::now()
        ]);
        
        // Permanently delete
        $session->permanentlyDelete();
        
        $this->info("✓ Session {$sessionId} permanently deleted with {$messageCount} messages");
        
    } catch (\Exception $e) {
        $this->error("Failed to delete session: " . $e->getMessage());
        return 1;
    }
    
    return 0;
    
})->purpose('Force delete a specific session permanently');