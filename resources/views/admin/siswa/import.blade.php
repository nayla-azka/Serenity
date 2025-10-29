@extends('admin.layouts.layout')

@section('content')
<section class="section">
    <div class="section-header text-black">
        <div class="section-header-back">
          <a href="{{ url('admin/siswa') }}" class="btn-back">
            <span class="btn-back__icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" height="20" width="20">
                <path d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z" fill="#fff"/>
                <path d="m237.248 512 265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z" fill="#fff"/>
              </svg>
            </span>
          </a>
        </div>
        <h2 class="section-title">Import Siswa</h2>
    </div>

    <div class="section-body">
      <!-- Year Gap Warning -->
      @if(session('year_gap_warning'))
      <div class="row">
        <div class="col-12">
          <div class="card border-danger" style="border-width: 3px;">
            <div class="card-header bg-danger text-white">
              <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> PERINGATAN: Terdeteksi Jeda Tahun Ajaran</h5>
            </div>
            <div class="card-body">
              <div class="alert alert-warning mb-3">
                <h6><i class="fas fa-info-circle"></i> Situasi Terdeteksi:</h6>
                <ul class="mb-0">
                  <li>Sistem memiliki siswa <strong>Grade XII/XIII</strong> sebanyak <strong>{{ (session('existing_stats')['grade_xii'] ?? 0) + (session('existing_stats')['grade_xiii'] ?? 0) }} siswa</strong></li>
                  <li>Anda mencoba import siswa <strong>Grade X</strong> (siswa baru)</li>
                  <li>Ini mengindikasikan sistem <strong>tidak digunakan minimal 1 tahun</strong></li>
                </ul>
              </div>

              <h6><i class="fas fa-question-circle"></i> Apa yang ingin Anda lakukan?</h6>
              
              <div class="row mt-3">
                <div class="col-md-6">
                  <div class="card border-danger">
                    <div class="card-body">
                      <h6 class="text-danger"><i class="fas fa-trash-alt"></i> Hapus Semua & Import Baru</h6>
                      <p style="font-size: 13px;">
                        Hapus <strong>SEMUA</strong> data siswa lama ({{ (session('existing_stats')['grade_x'] ?? 0) + (session('existing_stats')['grade_xi'] ?? 0) + (session('existing_stats')['grade_xii'] ?? 0) + (session('existing_stats')['grade_xiii'] ?? 0) }} siswa) 
                        dan import data baru. Cocok untuk memulai tahun ajaran baru setelah jeda.
                      </p>
                      <form action="{{ route('admin.siswa.import.process') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirmReplace()">
                        @csrf
                        <input type="hidden" name="force_replace" value="1">
                        
                        <!-- Re-upload file -->
                        <div class="mb-2">
                          <label class="form-label" style="font-size: 12px;">Upload ulang file Excel:</label>
                          <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx,.xls" required>
                        </div>
                        
                        <button type="submit" class="btn btn-danger w-100 mt-2">
                          <i class="fas fa-exclamation-triangle"></i> Hapus Semua & Import
                        </button>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="card border-success">
                    <div class="card-body">
                      <h6 class="text-success"><i class="fas fa-plus"></i> Tambahkan ke Data Lama</h6>
                      <p style="font-size: 13px;">
                        Tetap simpan data siswa lama dan tambahkan siswa baru. 
                        Siswa dengan NIS yang sama akan dilewati otomatis.
                      </p>
                      <form action="{{ route('admin.siswa.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="force_replace" value="0">
                        
                        <!-- Re-upload file -->
                        <div class="mb-2">
                          <label class="form-label" style="font-size: 12px;">Upload ulang file Excel:</label>
                          <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx,.xls" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 mt-2">
                          <i class="fas fa-plus"></i> Tambahkan Saja
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              <div class="text-center mt-3">
                <a href="{{ route('admin.siswa.import') }}" class="btn btn-secondary">
                  <i class="fas fa-times"></i> Batalkan Import
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif

      <!-- How It Works Card -->
      <div class="row mb-3">
        <div class="col-12">
          <div class="card" style="background-color: #d1ecf1; border-left: 4px solid #0dcaf0;">
            <div class="card-body">
              <h5><i class="fas fa-lightbulb"></i> Cara Kerja Import (Auto-Skip Duplicate)</h5>
              <ul class="mb-0" style="font-size: 14px; line-height: 1.8;">
                <li>✅ <strong>Otomatis Deteksi Duplikat:</strong> Sistem akan cek NIS setiap siswa di Excel</li>
                <li>✅ <strong>Skip Jika Sudah Ada:</strong> Jika NIS sudah terdaftar, siswa dilewati (tidak error)</li>
                <li>✅ <strong>Import Hanya yang Baru:</strong> Hanya siswa dengan NIS baru yang akan diimport</li>
                <li>✅ <strong>No Data Loss:</strong> Data siswa lama tetap aman, tidak akan terhapus atau berubah</li>
                <li>✅ <strong>Fleksibel:</strong> Bisa import siswa baru (Grade X) maupun update dengan file lengkap (semua grade)</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Normal Import Form -->
      <div class="row mb-3">
        <div class="col-12">
          <div class="card" style="background-color: rgb(222, 220, 240);">
            <div class="card-header">
              <h5><i class="fas fa-file-import"></i> Import Data Siswa dari Excel</h5>
            </div>
            <div class="card-body">
              <form action="{{ route('admin.siswa.import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- File Upload -->
                <div class="form-group row mb-4">
                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">
                    File Excel <span class="text-danger">*</span>
                  </label>
                  <div class="col-sm-12 col-md-7">
                    <input type="file" name="file" class="form-control input-style @error('file') is-invalid @enderror" 
                           accept=".xlsx,.xls" required>
                    <small class="text-muted">
                      Format: .xlsx atau .xls (maksimal 10MB)<br>
                      <strong>Tip:</strong> Bisa upload file dengan semua grade, sistem otomatis skip NIS yang sudah ada.
                    </small>
                    @error('file')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="mt-2">
                      <a href="{{ route('admin.siswa.download.template') }}" class="btn btn-sm btn-info">
                        <i class="fas fa-download"></i> Download Template
                      </a>
                    </div>
                  </div>
                </div>

                <!-- Default Photo -->
                <div class="form-group row mb-4">
                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">
                    Foto Default
                  </label>
                  <div class="col-sm-12 col-md-7">
                    <div id="image-preview" class="image-preview border rounded d-flex align-items-center justify-content-center input-style" 
                         style="width: 200px; height: 200px; background-size: cover; background-position: center center;">
                      <span class="text-muted">No Image</span>
                    </div>
                    <input type="file" name="default_photo" id="image-upload" class="form-control input-style mt-2 @error('default_photo') is-invalid @enderror" 
                           accept="image/*">
                    <small class="text-muted">
                      Foto ini akan digunakan untuk semua siswa <strong>baru</strong> yang diimport. 
                      Kosongkan untuk menggunakan foto default sistem.
                    </small>
                    @error('default_photo')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <!-- Info Alert -->
                <div class="form-group row mb-4">
                  <div class="offset-md-3 col-md-7">
                    <div class="alert alert-success" style="border-left: 4px solid #28a745;">
                      <h6><i class="fas fa-check-circle"></i> Keunggulan Import Otomatis:</h6>
                      <ul class="mb-0" style="font-size: 13px;">
                        <li><strong>Anti-Duplikat:</strong> Siswa dengan NIS yang sama otomatis dilewati</li>
                        <li><strong>Flexible:</strong> Bisa import siswa baru atau update file lengkap</li>
                        <li><strong>Safe:</strong> Data lama tidak akan berubah atau terhapus</li>
                        <li><strong>Fast:</strong> Proses bulk insert untuk performa optimal</li>
                        <li><strong>Transparent:</strong> Laporan lengkap: berapa diimport, berapa dilewati</li>
                      </ul>
                    </div>
                  </div>
                </div>

                <!-- Additional Info -->
                <div class="form-group row mb-4">
                  <div class="offset-md-3 col-md-7">
                    <div class="alert alert-info">
                      <h6><i class="fas fa-info-circle"></i> Catatan Penting:</h6>
                      <ul class="mb-0" style="font-size: 13px;">
                        <li>Password akan digenerate otomatis dan dapat didownload setelah import</li>
                        <li>Pastikan format Excel sesuai dengan template (kolom B=NIS, C=Nama, E=Kelas)</li>
                        <li>Nama kelas harus sudah terdaftar di sistem</li>
                        <li>NIS yang sudah ada akan dilewati (tidak error)</li>
                        <li>Proses import mungkin memakan waktu untuk file besar</li>
                      </ul>
                    </div>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="form-group row mb-4">
                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                  <div class="col-sm-12 col-md-7">
                    <button type="submit" class="dt dt-btn create">
                      <i class="fas fa-file-import me-2"></i> Import Sekarang
                    </button>
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">Batal</a>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Use Cases Examples -->
      <div class="row mb-3">
        <div class="col-12">
          <div class="card" style="background-color: rgb(222, 220, 240);">
            <div class="card-header">
              <h5><i class="fas fa-question-circle"></i> Kapan Menggunakan Import?</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="card bg-light border">
                    <div class="card-body">
                      <h6 class="text-primary"><i class="fas fa-user-plus"></i> Siswa Baru (Grade X)</h6>
                      <p style="font-size: 13px;" class="mb-0">
                        Upload file berisi siswa Grade X baru. 
                        Sistem akan skip NIS yang sudah ada dan hanya import yang baru.
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card bg-light border">
                    <div class="card-body">
                      <h6 class="text-success"><i class="fas fa-sync-alt"></i> Update Data Lengkap</h6>
                      <p style="font-size: 13px;" class="mb-0">
                        Upload file dengan semua grade (X, XI, XII). 
                        Sistem hanya import siswa dengan NIS baru, skip yang sudah ada.
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card bg-light border">
                    <div class="card-body">
                      <h6 class="text-warning"><i class="fas fa-redo"></i> Tahun Ajaran Baru</h6>
                      <p style="font-size: 13px;" class="mb-0">
                        Jalankan "Kenaikan Tahun" dulu, lalu import Grade X baru. 
                        Atau gunakan opsi "Hapus & Import" jika ada jeda.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Instructions -->
      <div class="row">
        <div class="col-12">
          <div class="card" style="background-color: rgb(222, 220, 240);">
            <div class="card-header">
              <h5><i class="fas fa-book"></i> Panduan Import Step-by-Step</h5>
            </div>
            <div class="card-body">
              <ol style="font-size: 14px; line-height: 1.8;">
                <li><strong>Download Template</strong>
                  <ul>
                    <li>Klik tombol "Download Template" untuk mendapatkan format Excel yang benar</li>
                  </ul>
                </li>
                <li><strong>Isi Data Siswa</strong>
                  <ul>
                    <li><strong>Kolom B (NIS):</strong> Nomor Induk Siswa - WAJIB diisi, harus unik</li>
                    <li><strong>Kolom C (Nama):</strong> Nama lengkap siswa - WAJIB diisi</li>
                    <li><strong>Kolom E (Kls):</strong> Nama kelas sesuai database - WAJIB diisi (contoh: X RPL 1, XI TKJ 2)</li>
                    <li>Kolom A (No Absen) dan D (L/P) tidak diproses sistem (opsional)</li>
                  </ul>
                </li>
                <li><strong>Upload File</strong>
                  <ul>
                    <li>Pilih file Excel yang sudah diisi</li>
                    <li>Opsional: Upload foto default untuk siswa baru</li>
                  </ul>
                </li>
                <li><strong>Klik Import</strong>
                  <ul>
                    <li>Sistem akan proses file dan skip NIS yang sudah ada</li>
                    <li>Tunggu hingga proses selesai (bisa beberapa menit untuk file besar)</li>
                  </ul>
                </li>
                <li><strong>Download Password</strong>
                  <ul>
                    <li>Setelah import berhasil, download file password untuk siswa baru</li>
                    <li>Bagikan password ke siswa yang bersangkutan</li>
                  </ul>
                </li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

@push('scripts')
<script>
$(document).ready(function () {
    $('#image-upload').on('change', function () {
        let input = this;
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('#image-preview')
                    .css('background-image', 'url(' + e.target.result + ')')
                    .html('');
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
});

function confirmReplace() {
    return confirm(
        '⚠️ PERINGATAN KRITIS! ⚠️\n\n' +
        'Anda akan MENGHAPUS SEMUA DATA SISWA LAMA!\n\n' +
        'Proses ini akan:\n' +
        '- Menghapus SEMUA siswa yang ada sekarang\n' +
        '- Menghapus akun user mereka\n' +
        '- Menghapus foto mereka\n' +
        '- TIDAK DAPAT DIBATALKAN!\n\n' +
        'Pastikan Anda sudah backup data jika diperlukan!\n\n' +
        'Apakah Anda YAKIN ingin melanjutkan?'
    );
}
</script>
@endpush
@endsection