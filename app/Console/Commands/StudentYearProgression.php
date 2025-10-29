<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student as Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentYearProgression extends Command
{
    protected $signature = 'students:progress-year {--dry-run : Preview changes without executing}';
    protected $description = 'Progress students to next year (handles repeating students & KA Grade XIII)';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }
        
        $this->info('Starting student year progression...');

        if (!$dryRun) {
            DB::beginTransaction();
        }

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
                    
                    if (!$dryRun) {
                        $student->update(['repeat_grade' => false]);
                    }
                    
                    $repeatingCount++;
                    continue;
                }

                $currentClass = $student->class->class_name;
                $normalizedClass = preg_replace('/\s+/', ' ', trim($currentClass));
                
                if (preg_match('/^(X{1,3}I{0,3})\s+([A-Z]+)\s*(\d*)$/i', $normalizedClass, $matches)) {
                    $grade = strtoupper($matches[1]); // X, XI, XII, XIII
                    $major = strtoupper($matches[2]); // RPL, TKJ, KA.
                    $classNumber = $matches[3] ?? '';
                } else {
                    $this->warn("Cannot parse class name: {$currentClass} for student {$student->nis}");
                    $skippedCount++;
                    continue;
                }

                // Handle graduation
                $isGraduated = $this->isGraduated($grade, $major);

                if ($isGraduated) {
                    $graduationDate = $student->updated_at;
                    $oneYearAgo = now()->subYear();

                    if ($graduationDate->lt($oneYearAgo)) {
                        if (!$dryRun) {
                            $user = $student->user;
                            
                            if ($student->photo && $student->photo !== 'default.jpg' && \Storage::disk('public')->exists($student->photo)) {
                                \Storage::disk('public')->delete($student->photo);
                            }
                            
                            $student->delete();
                            if ($user) {
                                $user->delete();
                            }
                        }
                        
                        $deletedCount++;
                        $this->info("Deleted: {$student->student_name} (NIS: {$student->nis}) - Graduated over 1 year ago");
                        continue;
                    } else {
                        $this->comment("Skipped: {$student->student_name} - Already graduated");
                        $skippedCount++;
                        continue;
                    }
                }

                // Progress to next grade
                $nextGrade = $this->getNextGrade($grade, $major);
                
                if (!$nextGrade) {
                    $this->comment("Skipped: {$student->student_name} - At final grade");
                    $skippedCount++;
                    continue;
                }

                // Find next class
                $nextClassName = $nextGrade . ' ' . $major . ($classNumber ? ' ' . $classNumber : '');
                $nextClass = Kelas::where('class_name', $nextClassName)->first();

                if (!$nextClass && $classNumber) {
                    $nextClassNameAlt = $nextGrade . ' ' . $major;
                    $nextClass = Kelas::where('class_name', 'LIKE', $nextClassNameAlt . '%')
                        ->orderBy('class_name')
                        ->first();
                }

                if (!$nextClass) {
                    $nextClass = Kelas::where('class_name', 'LIKE', $nextGrade . ' ' . $major . '%')
                        ->orderBy('class_name')
                        ->first();
                }

                if ($nextClass) {
                    if (!$dryRun) {
                        $student->update(['class_id' => $nextClass->id_class]);
                    }
                    $progressedCount++;
                    $this->info("Progressed: {$student->student_name} from {$currentClass} to {$nextClass->class_name}");
                } else {
                    $this->warn("Next class not found for: {$student->student_name} (expected: {$nextClassName})");
                    $skippedCount++;
                }
            }

            if (!$dryRun) {
                DB::commit();
            }

            $this->newLine();
            $this->info('=== Summary ===');
            $this->info("Students progressed: {$progressedCount}");
            $this->info("Students repeating grade: {$repeatingCount}");
            $this->info("Students deleted: {$deletedCount}");
            $this->info("Students skipped: {$skippedCount}");
            $this->info("Total processed: " . $students->count());
            $this->newLine();
            
            if ($dryRun) {
                $this->warn('This was a DRY RUN - no actual changes were made');
                $this->info('Run without --dry-run flag to execute changes');
            } else {
                $this->info('Year progression completed successfully!');
            }

            Log::info('Student year progression completed', [
                'progressed' => $progressedCount,
                'repeating' => $repeatingCount,
                'deleted' => $deletedCount,
                'skipped' => $skippedCount,
                'total' => $students->count(),
                'dry_run' => $dryRun,
                'date' => now()->toDateTimeString()
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            if (!$dryRun) {
                DB::rollBack();
            }
            
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
     * Get next grade level (handles KA Grade XIII)
     */
    private function getNextGrade($currentGrade, $major)
    {
        // Special handling for KA major (has Grade XIII)
        if ($major === 'KA') {
            $gradeMapKA = [
                'X' => 'XI',
                'XI' => 'XII',
                'XII' => 'XIII',
                'XIII' => null, // Final grade for KA
            ];
            return $gradeMapKA[strtoupper($currentGrade)] ?? null;
        }
        
        // Standard 3-year programs (RPL, TKJ.)
        $gradeMap = [
            'X' => 'XI',
            'XI' => 'XII',
            'XII' => null, // Final grade
            'XIII' => null, // Should not happen for non-KA
        ];

        return $gradeMap[strtoupper($currentGrade)] ?? null;
    }

    /**
     * Check if student is graduated (handles KA Grade XIII)
     */
    private function isGraduated($grade, $major)
    {
        $supportedMajors = ['RPL', 'TKJ', 'KA'];
        
        if (!in_array($major, $supportedMajors)) {
            return false;
        }
        
        // KA graduates at Grade XIII
        if ($major === 'KA') {
            return $grade === 'XIII';
        }
        
        // Other majors graduate at Grade XII
        return $grade === 'XII';
    }
}