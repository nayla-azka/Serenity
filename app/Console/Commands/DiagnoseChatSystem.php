<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Student;
use App\Models\Counselor;
use App\Models\ChatSession;
use App\Models\ChatMessage;

class DiagnoseChatSystem extends Command
{
    protected $signature = 'chat:diagnose';
    protected $description = 'Diagnose chat system configuration and data integrity';

    public function handle()
    {
        $this->info('🔍 Chat System Diagnostic Report');
        $this->info('================================');

        // Check database tables
        $this->checkTables();
        
        // Check data integrity
        $this->checkDataIntegrity();
        
        // Check environment configuration
        $this->checkEnvironment();
        
        // Check model relationships
        $this->checkRelationships();
        
        $this->info('✅ Diagnostic complete!');
    }

    private function checkTables()
    {
        $this->info("\n📊 Database Tables:");
        
        $tables = ['users', 'student', 'counselor', 'chat_sessions', 'chat_messages'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                $this->line("✓ {$table}: {$count} records");
            } else {
                $this->error("✗ {$table}: Table missing!");
            }
        }
    }

    private function checkDataIntegrity()
    {
        $this->info("\n🔗 Data Integrity:");
        
        // Check users with student/counselor profiles
        $usersCount = User::count();
        $studentsCount = Student::count();
        $counselorsCount = Counselor::count();
        
        $this->line("Users: {$usersCount}");
        $this->line("Students: {$studentsCount}");
        $this->line("Counselors: {$counselorsCount}");
        
        // Check orphaned records
        $orphanedStudents = Student::whereNotIn('user_id', User::pluck('id'))->count();
        $orphanedCounselors = Counselor::whereNotIn('user_id', User::pluck('id'))->count();
        
        if ($orphanedStudents > 0) {
            $this->warn("⚠️  {$orphanedStudents} students without user accounts");
        }
        
        if ($orphanedCounselors > 0) {
            $this->warn("⚠️  {$orphanedCounselors} counselors without user accounts");
        }
        
        // Check active chat sessions
        $activeSessions = ChatSession::where('is_active', 1)->count();
        $totalMessages = ChatMessage::count();
        
        $this->line("Active chat sessions: {$activeSessions}");
        $this->line("Total messages: {$totalMessages}");
    }

    private function checkEnvironment()
    {
        $this->info("\n⚙️  Environment Configuration:");
        
        $pusherKey = env('PUSHER_APP_KEY');
        $pusherCluster = env('PUSHER_APP_CLUSTER');
        $broadcastDriver = env('BROADCAST_DRIVER');
        
        if ($pusherKey) {
            $this->line("✓ PUSHER_APP_KEY: " . substr($pusherKey, 0, 10) . "...");
        } else {
            $this->error("✗ PUSHER_APP_KEY: Not set");
        }
        
        if ($pusherCluster) {
            $this->line("✓ PUSHER_APP_CLUSTER: {$pusherCluster}");
        } else {
            $this->error("✗ PUSHER_APP_CLUSTER: Not set");
        }
        
        if ($broadcastDriver === 'pusher') {
            $this->line("✓ BROADCAST_DRIVER: {$broadcastDriver}");
        } else {
            $this->warn("⚠️  BROADCAST_DRIVER: {$broadcastDriver} (should be 'pusher')");
        }
    }

    private function checkRelationships()
    {
        $this->info("\n🔄 Model Relationships:");
        
        try {
            // Test a few relationships
            $testStudent = Student::with('user')->first();
            if ($testStudent && $testStudent->user) {
                $this->line("✓ Student->User relationship working");
            } else {
                $this->warn("⚠️  Student->User relationship issue");
            }
            
            $testCounselor = Counselor::with('user')->first();
            if ($testCounselor && $testCounselor->user) {
                $this->line("✓ Counselor->User relationship working");
            } else {
                $this->warn("⚠️  Counselor->User relationship issue");
            }
            
            $testSession = ChatSession::with(['student', 'counselor'])->first();
            if ($testSession) {
                $this->line("✓ ChatSession relationships working");
            } else {
                $this->warn("⚠️  No chat sessions to test relationships");
            }
            
        } catch (\Exception $e) {
            $this->error("✗ Relationship test failed: " . $e->getMessage());
        }
    }
}