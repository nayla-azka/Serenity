<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\SiswaDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Student as Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SiswaController extends AdminBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(SiswaDataTable $siswaDataTable)
    {
        return $siswaDataTable->render('admin.siswa.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelas = Kelas::all();
        return view('admin.siswa.create', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->tryCatchResponse(function () use ($request) {
            $request->validate([
                'nis' => 'required|unique:student,nis',
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'password' => 'required|min:6',
                'student_name' => 'required',
                'class_id' => 'required',
                'repeat_grade' => 'nullable|boolean',
            ]);

            // Create user with auto-generated email (not visible to student)
            $user = User::create([
                'name' => $request->student_name,
                'email' => $this->generateUniqueEmail(),
                'password' => Hash::make($request->password),
                'plain_password' => $request->password, // Store plain password for konselor export
                'role' => 'siswa',
            ]);

            $path = $request->hasFile('photo')
                ? $request->file('photo')->store('siswa', 'public')
                : 'default.jpg';

            // Create student profile
            Siswa::create([
                'nis' => $request->nis,
                'student_name' => $request->student_name,
                'photo' => $path,
                'class_id' => $request->class_id,
                'user_id' => $user->id,
                'repeat_grade' => $request->boolean('repeat_grade', false),
            ]);
        },
        'Data siswa berhasil ditambah!',
        'Gagal menambah siswa.',
        'admin.siswa.index'
        );
    }

    /**
     * Generate unique email for auto-generated student emails
     */
    private function generateUniqueEmail()
    {
        do {
            $email = 'siswa_' . Str::random(12) . '@serenity.local';
        } while (User::where('email', $email)->exists());

        return $email;
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        $kelas = Kelas::all();
        return view('admin.siswa.import', compact('kelas'));
    }

    /**
     * Import students from Excel with bulk insert (optimized for large files)
     */
    public function import(Request $request)
    {
        return $this->tryCatchResponse(function () use ($request) {
            set_time_limit(1800);
            ini_set('memory_limit', '2048M');

            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls|max:10240',
                'default_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'force_replace' => 'nullable|boolean',
            ]);

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $forceReplace = $request->boolean('force_replace', false);

            // SMART YEAR GAP DETECTION
            // Check if Grade XII or XIII exists (indicates old data from previous year)
            $hasGradeXII = Siswa::whereHas('class', function($q) {
                $q->where('class_name', 'LIKE', 'XII %');
            })->exists();

            $hasGradeXIII = Siswa::whereHas('class', function($q) {
                $q->where('class_name', 'LIKE', 'XIII %');
            })->exists();

            // Count existing students by grade
            $existingStats = [
                'grade_x' => Siswa::whereHas('class', function($q) {
                    $q->where('class_name', 'LIKE', 'X %');
                })->count(),
                'grade_xi' => Siswa::whereHas('class', function($q) {
                    $q->where('class_name', 'LIKE', 'XI %');
                })->count(),
                'grade_xii' => Siswa::whereHas('class', function($q) {
                    $q->where('class_name', 'LIKE', 'XII %');
                })->count(),
                'grade_xiii' => Siswa::whereHas('class', function($q) {
                    $q->where('class_name', 'LIKE', 'XIII %');
                })->count(),
            ];

            // Quick peek at import file to detect what grades are being imported
            $readerPeek = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($extension === 'xlsx' ? 'Xlsx' : 'Xls');
            $readerPeek->setReadDataOnly(true);
            $spreadsheetPeek = $readerPeek->load($file->getRealPath());
            $worksheetPeek = $spreadsheetPeek->getActiveSheet();

            $importingGrades = [
                'has_x' => false,
                'has_xi' => false,
                'has_xii' => false,
                'has_xiii' => false,
            ];

            // Check first 10 rows to detect grades
            $allKelasForPeek = Kelas::all()->keyBy('class_name');
            for ($peekRow = 2; $peekRow <= min(10, $worksheetPeek->getHighestRow()); $peekRow++) {
                $classInfo = trim($this->getCellValue($worksheetPeek, 4, $peekRow));
                if (!empty($classInfo)) {
                    $kelas = $this->extractAndFindClassFromMemory($classInfo, $allKelasForPeek);
                    if ($kelas) {
                        if (preg_match('/^X\s/i', $kelas->class_name)) {
                            $importingGrades['has_x'] = true;
                        } elseif (preg_match('/^XI\s/i', $kelas->class_name)) {
                            $importingGrades['has_xi'] = true;
                        } elseif (preg_match('/^XII\s/i', $kelas->class_name)) {
                            $importingGrades['has_xii'] = true;
                        } elseif (preg_match('/^XIII\s/i', $kelas->class_name)) {
                            $importingGrades['has_xiii'] = true;
                        }
                    }
                }
            }

            $spreadsheetPeek->disconnectWorksheets();
            unset($spreadsheetPeek, $worksheetPeek);

            // YEAR GAP DETECTION LOGIC
            // If importing Grade X but Grade XII/XIII exists = year gap likely
            $yearGapDetected = $importingGrades['has_x'] && ($hasGradeXII || $hasGradeXIII) && !$importingGrades['has_xi'] && !$importingGrades['has_xii'] && !$importingGrades['has_xiii'];

            if ($yearGapDetected && !$forceReplace) {
                \Log::warning('Year gap detected during import', [
                    'existing_stats' => $existingStats,
                    'importing_grades' => $importingGrades,
                    'user' => auth()->user()->name
                ]);

                return redirect()
                    ->route('admin.siswa.import')
                    ->with('year_gap_warning', true)
                    ->with('existing_stats', $existingStats)
                    ->with('warning',
                        'Terdeteksi siswa Grade XII/XIII masih ada di sistem (' . ($existingStats['grade_xii'] + $existingStats['grade_xiii']) . ' siswa). ' .
                        'Anda akan import siswa Grade X baru. ' .
                        'Sepertinya ada jeda tahun ajaran. Apakah Anda ingin menghapus semua data lama?'
                    );
            }

            // If force replace, delete all old students
            if ($forceReplace) {
                \Log::info('Force replacing all student data', [
                    'user' => auth()->user()->name,
                    'reason' => 'User confirmed year gap replacement'
                ]);

                $oldStudents = Siswa::with('user')->get();
                $deletedCount = $oldStudents->count();

                foreach ($oldStudents as $student) {
                    if ($student->photo && $student->photo !== 'default.jpg' && \Storage::disk('public')->exists($student->photo)) {
                        \Storage::disk('public')->delete($student->photo);
                    }

                    if ($student->user) {
                        $student->user->delete();
                    }

                    $student->delete();
                }

                \Log::info("Deleted {$deletedCount} old students due to year gap replacement");
            }

            // MAIN IMPORT LOGIC
            $imported = [];
            $errors = [];
            $skippedDuplicates = [];
            $skippedErrors = 0;

            $defaultPhotoPath = 'default.jpg';
            if ($request->hasFile('default_photo')) {
                $defaultPhotoPath = $request->file('default_photo')->store('siswa', 'public');
            }

            try {
                if (in_array($extension, ['xlsx', 'xls'])) {
                    $readerType = $extension === 'xlsx' ? 'Xlsx' : 'Xls';
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($readerType);
                    $reader->setReadDataOnly(true);
                    $reader->setReadEmptyCells(false);

                    $spreadsheet = $reader->load($file->getRealPath());
                    $worksheet = $spreadsheet->getActiveSheet();

                    $columnNIS = 1;
                    $columnNama = 2;
                    $columnKls = 4;

                    $highestRow = $worksheet->getHighestRow();

                    // Pre-load all classes and existing NIS
                    $allKelas = Kelas::all()->keyBy('class_name');
                    $existingNIS = Siswa::pluck('nis')->flip(); // Get all existing NIS in system
                    $generatedEmails = [];
                    $timestamp = now();

                    $validatedData = [];
                    \Log::info("Starting import: Total rows = " . $highestRow);
                    \Log::info("Existing NIS count in database: " . count($existingNIS));

                    // FIRST PASS: Validate and collect data
                    for ($rowNum = 2; $rowNum <= $highestRow; $rowNum++) {
                        try {
                            $nis = trim($this->getCellValue($worksheet, $columnNIS, $rowNum));
                            $name = trim($this->getCellValue($worksheet, $columnNama, $rowNum));
                            $classInfo = trim($this->getCellValue($worksheet, $columnKls, $rowNum));

                            // Skip completely empty rows
                            if (empty($nis) && empty($name) && empty($classInfo)) {
                                continue;
                            }

                            // Validation: NIS required
                            if (empty($nis)) {
                                $errors[] = "Baris $rowNum: NIS kosong";
                                $skippedErrors++;
                                continue;
                            }

                            // Validation: Name required
                            if (empty($name)) {
                                $errors[] = "Baris $rowNum: Nama kosong";
                                $skippedErrors++;
                                continue;
                            }

                            // Validation: Class required
                            if (empty($classInfo)) {
                                $errors[] = "Baris $rowNum: Kelas kosong";
                                $skippedErrors++;
                                continue;
                            }

                            // Find class in memory
                            $kelas = $this->extractAndFindClassFromMemory($classInfo, $allKelas);
                            if (!$kelas) {
                                $errors[] = "Baris $rowNum: Kelas '$classInfo' tidak ditemukan di database";
                                $skippedErrors++;
                                continue;
                            }

                            // CHECK FOR DUPLICATE NIS - This is the key logic!
                            if (isset($existingNIS[$nis])) {
                                $skippedDuplicates[] = [
                                    'row' => $rowNum,
                                    'nis' => $nis,
                                    'name' => $name,
                                    'class' => $kelas->class_name
                                ];
                                \Log::info("Skipping duplicate NIS: $nis (row $rowNum)");
                                continue; // Skip this student, don't add to validated data
                            }

                            // Add to validated data (new student)
                            $validatedData[] = [
                                'nis' => $nis,
                                'name' => $name,
                                'kelas' => $kelas,
                            ];

                            // Mark this NIS as "used" to prevent duplicates within the same import file
                            $existingNIS[$nis] = true;

                        } catch (\Exception $e) {
                            $errors[] = "Baris $rowNum: " . $e->getMessage();
                            \Log::error("Error on row $rowNum: " . $e->getMessage());
                            $skippedErrors++;
                            continue;
                        }
                    }

                    \Log::info("Validated " . count($validatedData) . " new students");
                    \Log::info("Skipped " . count($skippedDuplicates) . " duplicates");
                    \Log::info("Skipped " . $skippedErrors . " errors");

                    // Clean up spreadsheet to free memory
                    $spreadsheet->disconnectWorksheets();
                    unset($spreadsheet, $worksheet);
                    gc_collect_cycles();

                    // SECOND PASS: Prepare data for bulk insert
                    $usersToInsert = [];
                    $studentsToInsert = [];
                    $passwordMap = [];

                    foreach ($validatedData as $data) {
                        $password = $this->generatePassword();
                        $hashedPassword = Hash::make($password);
                        $email = $this->generateUniqueEmailInMemory($generatedEmails);

                        $usersToInsert[] = [
                            'name' => $data['name'],
                            'email' => $email,
                            'password' => $hashedPassword,
                            'plain_password' => $password,
                            'role' => 'siswa',
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ];

                        $studentsToInsert[] = [
                            'nis' => $data['nis'],
                            'student_name' => $data['name'],
                            'photo' => $defaultPhotoPath,
                            'class_id' => $data['kelas']->id_class,
                            'email' => $email,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ];

                        $passwordMap[$data['nis']] = [
                            'nis' => $data['nis'],
                            'name' => $data['name'],
                            'class' => $data['kelas']->class_name,
                            'password' => $password,
                        ];
                    }

                    \Log::info("Prepared " . count($usersToInsert) . " users for insertion");

                    // THIRD PASS: Bulk insert with transaction
                    if (!empty($usersToInsert) && !empty($studentsToInsert)) {
                        DB::beginTransaction();

                        try {
                            $chunkSize = 500;

                            // Insert users
                            \Log::info("Starting user insertion...");
                            foreach (array_chunk($usersToInsert, $chunkSize) as $chunkIndex => $chunk) {
                                User::insert($chunk);
                                \Log::info("Inserted user chunk " . ($chunkIndex + 1));
                            }

                            // Map users to students
                            \Log::info("Mapping users to students...");
                            $newUsers = User::whereIn('email', array_column($usersToInsert, 'email'))
                                ->pluck('id', 'email')
                                ->toArray();

                            foreach ($studentsToInsert as $key => &$student) {
                                $userEmail = $student['email'];

                                if (!isset($newUsers[$userEmail])) {
                                    \Log::error("User not found for email: $userEmail");
                                    unset($studentsToInsert[$key]);
                                    continue;
                                }

                                $student['user_id'] = $newUsers[$userEmail];
                                unset($student['email']);
                            }
                            unset($student);

                            $studentsToInsert = array_values($studentsToInsert);

                            // Insert students
                            \Log::info("Starting student insertion...");
                            foreach (array_chunk($studentsToInsert, $chunkSize) as $chunkIndex => $chunk) {
                                Siswa::insert($chunk);
                                \Log::info("Inserted student chunk " . ($chunkIndex + 1));
                            }

                            DB::commit();
                            \Log::info("Transaction committed successfully");

                            $imported = array_values($passwordMap);

                        } catch (\Exception $e) {
                            DB::rollBack();
                            \Log::error('Bulk insert error: ' . $e->getMessage());
                            \Log::error('Stack trace: ' . $e->getTraceAsString());
                            throw new \Exception('Database insert failed: ' . $e->getMessage());
                        }
                    }

                    // Store results in session
                    session(['imported_students' => $imported]);
                    if (!empty($skippedDuplicates)) {
                        session(['skipped_duplicates' => $skippedDuplicates]);
                    }

                    // Build success message
                    $message = count($imported) . " siswa baru berhasil diimport!";

                    if ($forceReplace) {
                        $message .= " Data siswa lama telah dihapus dan diganti.";
                    }

                    if (count($skippedDuplicates) > 0) {
                        $message .= " " . count($skippedDuplicates) . " siswa dilewati (NIS sudah terdaftar).";
                    }

                    if ($skippedErrors > 0) {
                        $message .= " " . $skippedErrors . " baris tidak valid.";
                    }

                    if (!empty($errors)) {
                        session(['import_errors' => $errors]);
                    }

                    return redirect()
                        ->route('admin.siswa.index')
                        ->with('success', $message)
                        ->with('show_download', count($imported) > 0)
                        ->with('show_duplicates', count($skippedDuplicates) > 0);
                }
            } catch (\Exception $e) {
                \Log::error('Import Error: ' . $e->getMessage());
                \Log::error('Stack trace: ' . $e->getTraceAsString());
                throw $e;
            }
        },
        'Berhasil mengimport data siswa.',
        'Gagal mengimport data siswa.',
        'admin.siswa.import'
        );
    }

    /**
     * Generate unique email in memory (no database queries)
     */
    private function generateUniqueEmailInMemory(&$generatedEmails)
    {
        do {
            $email = 'siswa_' . Str::random(12) . '@serenity.local';
        } while (isset($generatedEmails[$email]));

        $generatedEmails[$email] = true;
        return $email;
    }

    /**
     * Extract and find class from memory (pre-loaded kelas)
     */
    private function extractAndFindClassFromMemory($klsLp, $allKelas)
    {
        if (empty($klsLp)) {
            return null;
        }

        $klsLp = trim($klsLp);

        // Direct lookup first
        if ($allKelas->has($klsLp)) {
            return $allKelas[$klsLp];
        }

        // Try case-insensitive exact match
        foreach ($allKelas as $kelas) {
            if (strcasecmp($kelas->class_name, $klsLp) === 0) {
                return $kelas;
            }
        }

        // Fuzzy search through memory
        foreach ($allKelas as $kelas) {
            if (stripos($kelas->class_name, $klsLp) !== false) {
                return $kelas;
            }
        }

        return null;
    }

    /**
     * Get cell value by column index and row number
     */
    private function getCellValue($worksheet, $columnIndex, $rowNum)
    {
        try {
            // Convert 0-based index to 1-based for PhpSpreadsheet
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);
            $cellReference = $columnLetter . $rowNum;
            $cell = $worksheet->getCell($cellReference);

            $value = $cell->getCalculatedValue();
            if ($value === null || $value === '') {
                $value = $cell->getValue();
            }

            return trim($value ?? '');
        } catch (\Exception $e) {
            \Log::error("Error reading cell $columnIndex:$rowNum - " . $e->getMessage());
            return '';
        }
    }

    /**
     * Generate random password
     */
    private function generatePassword($length = 10)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $password;
    }

    /**
     * Export ALL students to Excel with their current passwords (KONSELOR ONLY)
     * This exports all existing students with their actual passwords visible
     */
    public function exportAllStudents()
    {
        // CRITICAL: Only konselor can access this feature
        if (!auth()->user()->isKonselor()) {
            abort(403, 'Unauthorized action. Only Konselor can export student data with passwords.');
        }

        $students = Siswa::with(['class', 'user'])->orderBy('class_id')->orderBy('student_name')->get();

        if ($students->isEmpty()) {
            return redirect()
                ->route('admin.siswa.index')
                ->with('error', 'Tidak ada data siswa untuk diexport.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);

        // Set header
        $headers = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Email', 'Password', 'Tanggal Dibuat'];
        foreach ($headers as $index => $header) {
            $colLetter = chr(65 + $index); // A, B, C, D, E, F, G
            $sheet->setCellValue($colLetter . '1', $header);
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D32F2F']], // Red for sensitive data
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Add data
        $row = 2;
        $no = 1;
        foreach ($students as $student) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValueExplicit('B' . $row, $student->nis, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row, $student->student_name);
            $sheet->setCellValue('D' . $row, $student->class->class_name ?? '-');
            $sheet->setCellValue('E' . $row, $student->user->email ?? '-');

            // Display actual password (stored in plain text in a separate field)
            // NOTE: If passwords are hashed, you'll need to track them separately
            $password = $student->user->plain_password ?? '[Encrypted - Cannot Display]';
            $sheet->setCellValueExplicit('F' . $row, $password, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            $sheet->setCellValue('G' . $row, $student->created_at ? $student->created_at->format('d-M-Y H:i') : '-');

            // Alternating row colors
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F0F0F0']
                    ]
                ]);
            }

            $row++;
        }

        // Add borders
        $sheet->getStyle('A1:G' . ($row - 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Add summary
        $summaryRow = $row + 1;
        $sheet->setCellValue('A' . $summaryRow, 'Total Siswa:');
        $sheet->setCellValue('B' . $summaryRow, $students->count() . ' siswa');
        $sheet->getStyle('A' . $summaryRow . ':B' . $summaryRow)->getFont()->setBold(true);

        // Add security warning
        $warningRow = $row + 3;
        $sheet->setCellValue('A' . $warningRow, '⚠️ PERINGATAN KEAMANAN:');
        $sheet->getStyle('A' . $warningRow)->getFont()
            ->setBold(true)
            ->setSize(12)
            ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0000'));

        $warningRow++;
        $sheet->setCellValue('A' . $warningRow, '• File ini berisi PASSWORD SENSITIF - jangan bagikan ke pihak tidak berwenang');
        $sheet->setCellValue('A' . ($warningRow + 1), '• Simpan file ini di lokasi yang AMAN dan TERENKRIPSI');
        $sheet->setCellValue('A' . ($warningRow + 2), '• Hapus file ini setelah tidak dibutuhkan');
        $sheet->setCellValue('A' . ($warningRow + 3), '• Hanya KONSELOR yang boleh mengakses file ini');

        $sheet->getStyle('A' . $warningRow . ':A' . ($warningRow + 3))->getFont()
            ->setItalic(true)
            ->setSize(10)
            ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0000'));

        foreach (range($warningRow, $warningRow + 3) as $r) {
            $sheet->mergeCells('A' . $r . ':G' . $r);
        }

        // Add metadata
        $spreadsheet->getProperties()
            ->setCreator('Serenity System - Konselor')
            ->setTitle('Data Siswa dengan Password (RAHASIA)')
            ->setSubject('Daftar Siswa - CONFIDENTIAL')
            ->setDescription('Data lengkap siswa dengan password - HANYA UNTUK KONSELOR')
            ->setKeywords('siswa password confidential konselor')
            ->setCategory('Confidential Data');

        // Download
        $writer = new Xlsx($spreadsheet);
        $filename = 'RAHASIA_Data_Siswa_Password_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        $writer->save('php://output');
        exit;
    }

    /**
     * Download Excel template for import
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set simplified header (5 columns only)
        $headers = ['No Absen', 'NIS', 'Nama', 'L/P', 'Kls'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Add example data (3 rows)
        $exampleData = [
            ['1', '0012345678', 'John Doe', 'L', 'X RPL 1'],
            ['2', '0012345679', 'Jane Smith', 'P', 'X RPL 1'],
            ['3', '0012345680', 'Bob Johnson', 'L', 'X TKJ 2'],
        ];

        $row = 2;
        foreach ($exampleData as $data) {
            foreach ($data as $colIndex => $value) {
                $sheet->setCellValueByColumnAndRow($colIndex + 1, $row, $value);
            }
            $row++;
        }

        // Style example data
        $sheet->getStyle('A2:E4')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']],
        ]);

        // Add instructions
        $instructionRow = 6;
        $sheet->setCellValue('A' . $instructionRow, 'INSTRUKSI IMPORT (FORMAT BARU - DISEDERHANAKAN):');
        $sheet->getStyle('A' . $instructionRow)->getFont()->setBold(true)->setSize(12)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0000'));
        $sheet->mergeCells('A' . $instructionRow . ':E' . $instructionRow);

        $instructions = [
            '1. Format baru menggunakan 5 kolom sederhana: No Absen (A), NIS (B), Nama (C), L/P (D), Kls (E)',
            '2. Kolom yang WAJIB diisi: NIS, Nama, dan Kls',
            '3. Kolom No Absen dan L/P opsional (tidak diproses)',
            '4. NIS harus unik dan belum terdaftar',
            '5. Nama kelas harus sesuai dengan database (contoh: X RPL 1, X TKJ 2, XI RPL 1)',
            '6. Hapus baris contoh (baris 2-4) sebelum upload data sesungguhnya',
            '7. Header harus tetap di baris 1, data dimulai dari baris 2',
            '8. Password akan digenerate otomatis dan dapat didownload setelah import',
            '9. Gunakan filter "Grade X Only" untuk import siswa baru saja',
            '10. Format file: .xlsx atau .xls (maksimal 10MB)',
        ];

        $instructionStartRow = $instructionRow + 1;
        foreach ($instructions as $index => $instruction) {
            $currentRow = $instructionStartRow + $index;
            $sheet->setCellValue('A' . $currentRow, $instruction);
            $sheet->getStyle('A' . $currentRow)->getFont()->setSize(10);
            $sheet->mergeCells('A' . $currentRow . ':E' . $currentRow);
        }

        // Add note about columns
        $noteRow = $instructionStartRow + count($instructions) + 1;
        $sheet->setCellValue('A' . $noteRow, 'KOLOM YANG DIPROSES SISTEM:');
        $sheet->getStyle('A' . $noteRow)->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0000FF'));
        $sheet->mergeCells('A' . $noteRow . ':E' . $noteRow);

        $columnInfo = [
            '• Kolom B (NIS): Nomor Induk Siswa - digunakan untuk login',
            '• Kolom C (Nama): Nama lengkap siswa',
            '• Kolom E (Kls): Nama kelas sesuai database',
            '• Kolom A dan D: Tidak diproses sistem (opsional)',
        ];

        $columnInfoRow = $noteRow + 1;
        foreach ($columnInfo as $index => $info) {
            $currentRow = $columnInfoRow + $index;
            $sheet->setCellValue('A' . $currentRow, $info);
            $sheet->getStyle('A' . $currentRow)->getFont()->setSize(9)->setItalic(true);
            $sheet->mergeCells('A' . $currentRow . ':E' . $currentRow);
        }

        // Add available classes info
        $classRow = $columnInfoRow + count($columnInfo) + 1;
        $sheet->setCellValue('A' . $classRow, 'DAFTAR KELAS TERSEDIA DI SISTEM:');
        $sheet->getStyle('A' . $classRow)->getFont()->setBold(true)->setSize(11);
        $sheet->mergeCells('A' . $classRow . ':E' . $classRow);

        $availableClasses = Kelas::pluck('class_name')->toArray();
        if (!empty($availableClasses)) {
            $classListRow = $classRow + 1;
            $classList = '✓ ' . implode('  |  ', $availableClasses);
            $sheet->setCellValue('A' . $classListRow, $classList);
            $sheet->getStyle('A' . $classListRow)->getFont()->setSize(9);
            $sheet->getStyle('A' . $classListRow)->getAlignment()->setWrapText(true);
            $sheet->mergeCells('A' . $classListRow . ':E' . $classListRow);
            $sheet->getRowDimension($classListRow)->setRowHeight(-1); // Auto height
        }

        // Set column widths
        $widths = [12, 15, 30, 8, 20];
        foreach ($widths as $index => $width) {
            $sheet->getColumnDimensionByColumn($index + 1)->setWidth($width);
        }

        // Freeze header row
        $sheet->freezePane('A2');

        // Download
        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Siswa_Simplified_' . date('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $kelas = Kelas::all();
        $siswa = Siswa::with('user')->findOrFail($id);
        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Siswa $siswa)
    {
        return $this->tryCatchResponse(function () use ($request, $siswa) {
            $user = $siswa->user;

            $request->validate([
                'nis' => 'required|unique:student,nis,' . $siswa->id_student . ',id_student',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'password' => 'nullable|min:6',
                'student_name' => 'required',
                'class_id' => 'required',
                'repeat_grade' => 'nullable|boolean',
            ]);

            $path = $siswa->photo;

            if ($request->hasFile('photo')) {
                if ($siswa->photo && $siswa->photo !== 'default.jpg' && Storage::disk('public')->exists($siswa->photo)) {
                    Storage::disk('public')->delete($siswa->photo);
                }
                $path = $request->file('photo')->store('siswa', 'public');
            }

            // Update user data
            $userData = [
                'name' => $request->student_name,
            ];

            // Only update password if a new one is provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
                $userData['plain_password'] = $request->password; // Store plain password for konselor export
            }

            $user->update($userData);

            $siswa->update([
                'nis' => $request->nis,
                'student_name' => $request->student_name,
                'photo' => $path,
                'class_id' => $request->class_id,
                'repeat_grade' => $request->boolean('repeat_grade', false),
            ]);
        },
        'Data siswa berhasil diperbarui!',
        'Gagal mengedit siswa.',
        'admin.siswa.index'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Siswa $siswa)
    {
        return $this->tryCatchResponse(function () use ($siswa) {
            $user = $siswa->user;

            if ($siswa->photo && $siswa->photo !== 'default.jpg' && Storage::disk('public')->exists($siswa->photo)) {
                Storage::disk('public')->delete($siswa->photo);
            }

            $siswa->delete();
            if ($user) {
                $user->delete();
            }
        },
        'Data siswa berhasil dihapus!',
        'Gagal menghapus siswa.',
        'admin.siswa.index'
        );
    }

    /**
     * Show year progression page
     */
    public function showYearProgressionPage()
    {
        $stats = [
            'total_students' => Siswa::count(),
            'repeating_students' => Siswa::where('repeat_grade', true)->count(),
            'grade_x' => Siswa::whereHas('class', function($q) {
                $q->where('class_name', 'LIKE', 'X %');
            })->count(),
            'grade_xi' => Siswa::whereHas('class', function($q) {
                $q->where('class_name', 'LIKE', 'XI %');
            })->count(),
            'grade_xii' => Siswa::whereHas('class', function($q) {
                $q->where('class_name', 'LIKE', 'XII %');
            })->count(),
            'grade_xiii' => Siswa::whereHas('class', function($q) {
                $q->where('class_name', 'LIKE', 'XIII %');
            })->count(),
        ];
        
        return view('admin.siswa.year-progression', compact('stats'));
    }

    /**
     * Execute year progression (manual trigger)
     */
    public function executeYearProgression(Request $request)
    {
        $dryRun = $request->boolean('dry_run', false);
        
        try {
            if ($dryRun) {
                // Run in dry-run mode
                \Artisan::call('students:progress-year', ['--dry-run' => true]);
            } else {
                // Run actual progression
                \Artisan::call('students:progress-year');
                
                // Store last execution info
                session([
                    'last_progression' => [
                        'date' => now()->format('d-M-Y H:i'),
                        'user' => auth()->user()->name,
                        'summary' => 'Berhasil dijalankan'
                    ]
                ]);
            }
            
            $output = \Artisan::output();
            
            // Log the action
            \Log::info($dryRun ? 'Year progression preview executed' : 'Year progression executed', [
                'user' => auth()->user()->name,
                'dry_run' => $dryRun,
                'output' => $output
            ]);
            
            $message = $dryRun 
                ? 'Preview kenaikan tahun berhasil! Lihat log untuk detailnya.' 
                : 'Kenaikan tahun berhasil diproses! Silakan cek data siswa.';
            
            return redirect()
                ->route('admin.siswa.year-progression')
                ->with('success', $message)
                ->with('output', $output);
                
        } catch (\Exception $e) {
            \Log::error('Year progression failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->route('admin.siswa.year-progression')
                ->with('error', 'Gagal memproses kenaikan tahun: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update repeat grade status
     */
    public function bulkUpdateRepeatGrade(Request $request)
    {
        return $this->tryCatchResponse(function () use ($request) {
            $request->validate([
                'student_ids' => 'required|array',
                'student_ids.*' => 'exists:student,id_student',
                'repeat_grade' => 'required|boolean',
            ]);

            $count = Siswa::whereIn('id_student', $request->student_ids)
                ->update(['repeat_grade' => $request->repeat_grade]);

            $status = $request->repeat_grade ? 'akan mengulang kelas' : 'akan naik kelas';
            
            return redirect()
                ->route('admin.siswa.index')
                ->with('success', "$count siswa berhasil ditandai $status!");
        },
        'Berhasil mengupdate status siswa', // Success message handled above
        'Gagal mengupdate status siswa.',
        'admin.siswa.index'
        );
    }
}
