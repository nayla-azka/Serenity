<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student as Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StudentYearProgression extends Command
{
    protected $signature = 'students:progress-year';
    protected $description = 'Progress students to next year (handles repeating students & KA Grade XIII)';

    public function handle()
    {
        $this->info('Starting student year progression...');

        DB::beginTransaction();

        try {
            $progressedCount = 0;
            $deletedCount = 0;
            $skippedCount = 0;
            $repeatingCount = 0;

            $students = Siswa::with('class')->get();

            foreach ($students as $student) {
                if (!$student->class) {
                    $this->warn("Student {$student->nis} has no class assigned");
                    $skippedCount++;
                    continue;
                }

                // Check if student is marked to repeat grade
                if ($student->repeat_grade) {
                    $this->comment("Repeating: {$student->student_name} (NIS: {$student->nis}) - Stays in {$student->class->class_name}");
                    
                    // Reset repeat_grade flag
                    $student->update(['repeat_grade' => false]);
                    
                    $repeatingCount++;
                    continue;
                }

                $currentClass = $student->class->class_name;
                $normalizedClass = preg_replace('/\s+/', ' ', trim($currentClass));
                
                // Parse class name with more flexible pattern
                // Matches: "XII RPL 1", "XII RPL", "X TKJ 2", "XIII KA", etc.
                if (preg_match('/^(X{1,3}I{0,3})\s+([A-Z]+)\s*(\d*)$/i', $normalizedClass, $matches)) {
                    $grade = strtoupper($matches[1]); // X, XI, XII, XIII
                    $major = strtoupper($matches[2]); // RPL, TKJ, KA
                    $classNumber = $matches[3] ?? '';
                    
                    $this->comment("Processing: {$student->student_name} | Grade: {$grade} | Major: {$major} | Class: {$currentClass}");
                } else {
                    $this->warn("Cannot parse class name: {$currentClass} for student {$student->nis}");
                    $skippedCount++;
                    continue;
                }

                // Check if student will graduate (is in final year)
                $willGraduate = $this->willGraduate($grade, $major);

                if ($willGraduate) {
                    try {
                        $this->deleteGraduatingStudent($student);
                        $deletedCount++;
                        $this->info("Deleted (Graduated): {$student->student_name} (NIS: {$student->nis}) - Was in {$currentClass}");
                    } catch (\Exception $e) {
                        $this->error("Failed to delete student {$student->nis}: " . $e->getMessage());
                        Log::error("Failed to delete graduating student", [
                            'student_id' => $student->id_student,
                            'nis' => $student->nis,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        $skippedCount++;
                    }
                    continue;
                }

                // Progress to next grade
                $nextGrade = $this->getNextGrade($grade, $major);
                
                if (!$nextGrade) {
                    $this->comment("Skipped: {$student->student_name} - At final grade but not graduated yet");
                    $skippedCount++;
                    continue;
                }

                // Find next class
                $nextClassName = $nextGrade . ' ' . $major . ($classNumber ? ' ' . $classNumber : '');
                $nextClass = Kelas::where('class_name', $nextClassName)->first();

                // Fallback: try without class number
                if (!$nextClass && $classNumber) {
                    $nextClassNameAlt = $nextGrade . ' ' . $major;
                    $nextClass = Kelas::where('class_name', 'LIKE', $nextClassNameAlt . '%')
                        ->orderBy('class_name')
                        ->first();
                }

                // Fallback: try fuzzy match
                if (!$nextClass) {
                    $nextClass = Kelas::where('class_name', 'LIKE', $nextGrade . ' ' . $major . '%')
                        ->orderBy('class_name')
                        ->first();
                }

                if ($nextClass) {
                    $student->update(['class_id' => $nextClass->id_class]);
                    $progressedCount++;
                    $this->info("Progressed: {$student->student_name} from {$currentClass} to {$nextClass->class_name}");
                } else {
                    $this->warn("Next class not found for: {$student->student_name} (expected: {$nextClassName})");
                    $skippedCount++;
                }
            }

            DB::commit();

            $this->newLine();
            $this->info('=== Summary ===');
            $this->info("Students progressed: {$progressedCount}");
            $this->info("Students repeating grade: {$repeatingCount}");
            $this->info("Students deleted (graduated): {$deletedCount}");
            $this->info("Students skipped: {$skippedCount}");
            $this->info("Total processed: " . $students->count());
            $this->newLine();
            $this->info('Year progression completed successfully!');

            Log::info('Student year progression completed', [
                'progressed' => $progressedCount,
                'repeating' => $repeatingCount,
                'deleted' => $deletedCount,
                'skipped' => $skippedCount,
                'total' => $students->count(),
                'date' => now()->toDateTimeString()
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->error('Error during year progression: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            
            Log::error('Student year progression failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }

    /**
     * Delete graduating student and all related data
     */
    private function deleteGraduatingStudent($student)
    {
        $user = $student->user;
        $studentNIS = $student->nis;
        $studentName = $student->student_name;
        
        // CRITICAL: Delete in correct order to avoid foreign key constraint violations
        
        // 1. Delete chat-related records first (deepest dependencies)
        $chatSessions = DB::table('chat_sessions')
            ->where('id_student', $student->id_student)
            ->get();
        
        foreach ($chatSessions as $session) {
            // Delete chat messages
            DB::table('chat_messages')
                ->where('id_session', $session->id_session)
                ->delete();
            
            // Delete session views
            DB::table('session_views')
                ->where('id_session', $session->id_session)
                ->delete();
            
            // Delete chat session
            DB::table('chat_sessions')
                ->where('id_session', $session->id_session)
                ->delete();
        }
        
        // 2. Delete student's photo if exists
        if ($student->photo && $student->photo !== 'default.jpg' && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }
        
        // 3. Delete student record (this should cascade to related tables)
        $student->delete();
        
        // 4. Delete user record and let cascading handle the rest
        // These will cascade automatically based on your schema:
        // - article_views (ON DELETE SET NULL)
        // - articles (ON DELETE CASCADE) 
        // - comment_replies (ON DELETE CASCADE)
        // - comment_reports (ON DELETE CASCADE/SET NULL)
        // - comments (ON DELETE CASCADE)
        // - likes (ON DELETE CASCADE)
        // - reports (ON DELETE SET NULL)
        // - visits (ON DELETE CASCADE)
        if ($user) {
            $user->delete();
        }
    }

    /**
     * Get next grade level (handles KA Grade XIII)
     */
    private function getNextGrade($currentGrade, $major)
    {
        $currentGrade = strtoupper($currentGrade);
        $major = strtoupper($major);
        
        // Special handling for KA major (has Grade XIII)
        if ($major === 'KA') {
            $gradeMapKA = [
                'X' => 'XI',
                'XI' => 'XII',
                'XII' => 'XIII',
                'XIII' => null, // Graduated
            ];
            return $gradeMapKA[$currentGrade] ?? null;
        }
        
        // Standard 3-year programs (RPL, TKJ)
        $gradeMap = [
            'X' => 'XI',
            'XI' => 'XII',
            'XII' => null, // Graduated
        ];

        return $gradeMap[$currentGrade] ?? null;
    }

    /**
     * Check if student will graduate this year (is currently in final grade)
     * Students in XII (RPL/TKJ) or XIII (KA) will be graduated
     */
    private function willGraduate($grade, $major)
    {
        $grade = strtoupper($grade);
        $major = strtoupper($major);
        
        $supportedMajors = ['RPL', 'TKJ', 'KA'];
        
        if (!in_array($major, $supportedMajors)) {
            return false;
        }
        
        // KA students graduate when they're in XIII (4-year program)
        if ($major === 'KA') {
            return $grade === 'XIII';
        }
        
        // RPL and TKJ students graduate when they're in XII (3-year programs)
        return $grade === 'XII';
    }
}