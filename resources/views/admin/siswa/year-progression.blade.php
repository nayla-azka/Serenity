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
        <h2 class="section-title">Kenaikan Tahun Ajaran</h2>
    </div>

    <div class="section-body">
      <!-- Info Card -->
      <div class="row">
        <div class="col-12">
          <div class="card" style="background-color: #d1ecf1; border-left: 4px solid #0dcaf0;">
            <div class="card-body">
              <h5><i class="fas fa-info-circle"></i> Tentang Kenaikan Tahun</h5>
              <p class="mb-0" style="font-size: 14px;">
                Proses ini akan secara otomatis menaikkan semua siswa ke kelas berikutnya (X → XI → XII). 
                Siswa yang ditandai "Mengulang" akan tetap di kelas yang sama. 
                Siswa lulusan (kelas XII lebih dari 1 tahun) akan dihapus otomatis.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="row">
        <div class="col-lg-3 col-md-6">
          <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-1" style="opacity: 0.9;">Total Siswa</h6>
                  <h2 class="mb-0">{{ $stats['total_students'] }}</h2>
                </div>
                <div class="icon" style="font-size: 48px; opacity: 0.3;">
                  <i class="fas fa-users"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="card-body text-white">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-1" style="opacity: 0.9;">Kelas X</h6>
                  <h2 class="mb-0">{{ $stats['grade_x'] }}</h2>
                </div>
                <div class="icon" style="font-size: 48px; opacity: 0.3;">
                  <i class="fas fa-user-graduate"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="card-body text-white">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-1" style="opacity: 0.9;">Kelas XI</h6>
                  <h2 class="mb-0">{{ $stats['grade_xi'] }}</h2>
                </div>
                <div class="icon" style="font-size: 48px; opacity: 0.3;">
                  <i class="fas fa-user-graduate"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
            <div class="card-body text-white">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-1" style="opacity: 0.9;">Kelas XII</h6>
                  <h2 class="mb-0">{{ $stats['grade_xii'] }}</h2>
                </div>
                <div class="icon" style="font-size: 48px; opacity: 0.3;">
                  <i class="fas fa-user-graduate"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        @if($stats['grade_xiii'] > 0)
        <div class="col-lg-3 col-md-6">
          <div class="card" style="background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);">
            <div class="card-body text-white">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-1" style="opacity: 0.9;">Kelas XIII (KA)</h6>
                  <h2 class="mb-0">{{ $stats['grade_xiii'] }}</h2>
                </div>
                <div class="icon" style="font-size: 48px; opacity: 0.3;">
                  <i class="fas fa-graduation-cap"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
      </div>

      <!-- Repeating Students Warning -->
      @if($stats['repeating_students'] > 0)
      <div class="row">
        <div class="col-12">
          <div class="alert alert-warning d-flex align-items-center" style="border-left: 4px solid #ffc107;">
            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
            <div>
              <strong>Perhatian!</strong> Ada <strong>{{ $stats['repeating_students'] }} siswa</strong> yang ditandai akan mengulang kelas. 
              Mereka tidak akan naik kelas saat proses dijalankan.
              <a href="{{ route('admin.siswa.index') }}" class="alert-link">Lihat daftar siswa →</a>
            </div>
          </div>
        </div>
      </div>
      @endif

      <!-- Action Cards -->
      <div class="row">
        <!-- Dry Run Card -->
        <div class="col-md-6">
          <div class="card" style="background-color: rgb(222, 220, 240);">
            <div class="card-header bg-info text-white">
              <h5 class="mb-0"><i class="fas fa-eye"></i> Preview Kenaikan (Dry Run)</h5>
            </div>
            <div class="card-body">
              <p style="font-size: 14px;">
                Lihat preview apa yang akan terjadi tanpa mengubah data apapun. 
                Sangat direkomendasikan untuk dijalankan terlebih dahulu.
              </p>
              <ul style="font-size: 13px;">
                <li>✅ Tidak mengubah data</li>
                <li>✅ Menampilkan daftar siswa yang akan naik kelas</li>
                <li>✅ Menampilkan siswa yang akan mengulang</li>
                <li>✅ Menampilkan siswa yang akan dihapus</li>
              </ul>
              <form action="{{ route('admin.siswa.year-progression.execute') }}" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="dry_run" value="1">
                <button type="submit" class="btn btn-info w-100 btn-lg">
                  <i class="fas fa-search"></i> Jalankan Preview
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- Execute Card -->
        <div class="col-md-6">
          <div class="card" style="background-color: rgb(222, 220, 240);">
            <div class="card-header bg-danger text-white">
              <h5 class="mb-0"><i class="fas fa-rocket"></i> Jalankan Kenaikan Tahun</h5>
            </div>
            <div class="card-body">
              <p style="font-size: 14px;">
                <strong>PERINGATAN:</strong> Proses ini akan mengubah data siswa secara permanen. 
                Pastikan Anda sudah menjalankan preview terlebih dahulu.
              </p>
              <ul style="font-size: 13px;" class="text-danger">
                <li><strong>Tidak dapat dibatalkan!</strong></li>
                <li>Semua siswa akan naik kelas</li>
                <li>Siswa "Mengulang" tetap di kelas sama</li>
                <li>Lulusan lama akan dihapus</li>
              </ul>
              <form action="{{ route('admin.siswa.year-progression.execute') }}" method="POST" 
                    class="mt-3" onsubmit="return confirmExecution()">
                @csrf
                <input type="hidden" name="dry_run" value="0">
                <button type="submit" class="btn btn-danger w-100 btn-lg">
                  <i class="fas fa-exclamation-triangle"></i> JALANKAN SEKARANG
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Last Execution Info -->
      @if(session('last_progression'))
      <div class="row">
        <div class="col-12">
          <div class="card" style="background-color: #d4edda; border-left: 4px solid #28a745;">
            <div class="card-body">
              <h6><i class="fas fa-check-circle"></i> Kenaikan Terakhir</h6>
              <p class="mb-0" style="font-size: 13px;">
                <strong>Tanggal:</strong> {{ session('last_progression.date') }}<br>
                <strong>Oleh:</strong> {{ session('last_progression.user') }}<br>
                <strong>Hasil:</strong> {{ session('last_progression.summary') }}
              </p>
            </div>
          </div>
        </div>
      </div>
      @endif

      <!-- Instructions -->
      <div class="row">
        <div class="col-12">
          <div class="card" style="background-color: rgb(222, 220, 240);">
            <div class="card-header">
              <h5 class="mb-0"><i class="fas fa-book"></i> Panduan Penggunaan</h5>
            </div>
            <div class="card-body">
              <ol style="font-size: 14px; line-height: 1.8;">
                <li><strong>Sebelum Kenaikan Tahun:</strong>
                  <ul>
                    <li>Tandai siswa yang akan mengulang kelas di menu Siswa</li>
                    <li>Pastikan data kelas untuk tahun berikutnya sudah dibuat (XI untuk X, XII untuk XI)</li>
                    <li>Backup data siswa jika diperlukan</li>
                  </ul>
                </li>
                <li><strong>Jalankan Preview:</strong>
                  <ul>
                    <li>Klik tombol "Jalankan Preview" untuk melihat hasil tanpa mengubah data</li>
                    <li>Periksa apakah hasilnya sesuai harapan</li>
                  </ul>
                </li>
                <li><strong>Jalankan Kenaikan:</strong>
                  <ul>
                    <li>Jika preview sudah sesuai, klik "JALANKAN SEKARANG"</li>
                    <li>Konfirmasi peringatan yang muncul</li>
                    <li>Tunggu hingga proses selesai</li>
                  </ul>
                </li>
                <li><strong>Setelah Kenaikan:</strong>
                  <ul>
                    <li>Periksa data siswa untuk memastikan semua benar</li>
                    <li>Import siswa baru (Grade X) jika ada</li>
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
function confirmExecution() {
    return confirm(
        '⚠️ PERINGATAN PENTING! ⚠️\n\n' +
        'Anda akan menjalankan proses KENAIKAN TAHUN AJARAN.\n\n' +
        'Proses ini akan:\n' +
        '- Menaikkan semua siswa ke kelas berikutnya\n' +
        '- Menghapus data lulusan lama\n' +
        '- TIDAK DAPAT DIBATALKAN!\n\n' +
        'Apakah Anda yakin ingin melanjutkan?'
    );
}
</script>
@endpush
@endsection