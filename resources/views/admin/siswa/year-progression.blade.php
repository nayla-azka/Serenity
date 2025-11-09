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
      <div class="row mb-4">
        <div class="col-12">
          <div class="alert mb-0" style="background: linear-gradient(135deg, rgba(131, 122, 182, 0.1), rgba(151, 140, 200, 0.05)); border-left: 4px solid #837ab6;">
            <h6 class="mb-2" style="color: #6f5b9a;"><i class="fas fa-info-circle"></i> Tentang Kenaikan Tahun</h6>
            <p class="mb-0" style="font-size: 13px; color: #555;">
              Proses ini akan secara otomatis menaikkan semua siswa ke kelas berikutnya (X → XI → XII). 
              Siswa yang ditandai "Mengulang" akan tetap di kelas yang sama. 
              Siswa lulusan (kelas XII lebih dari 1 tahun) akan dihapus otomatis.
            </p>
          </div>
        </div>
      </div>

      <!-- Instructions -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card" style="background-color: rgb(222, 220, 240); border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.08);">
            <div class="card-header" style="cursor: pointer; background: linear-gradient(135deg, rgba(131, 122, 182, 0.1), rgba(151, 140, 200, 0.05)); border-left: 3px solid #837ab6; border-bottom: 1px solid rgba(131, 122, 182, 0.2);" onclick="toggleInstructions()">
              <h6 class="mb-0" style="color: #6f5b9a;">
                <i class="fas fa-book"></i> Panduan Penggunaan 
                <i class="fas fa-chevron-down float-end" id="instructionIcon" style="font-size: 12px;"></i>
              </h6>
            </div>
            <div id="instructions" style="display: none;">
              <div class="card-body">
                <ol style="font-size: 13px; line-height: 1.8; margin-bottom: 0; color: #555; padding-left: 20px;">
                  <li class="mb-2"><strong>Sebelum Kenaikan:</strong> Tandai siswa mengulang & pastikan kelas tahun depan sudah ada</li>
                  <li class="mb-2"><strong>Cek Statistik:</strong> Periksa jumlah siswa per kelas di bawah</li>
                  <li class="mb-2"><strong>Jalankan Kenaikan:</strong> Klik "JALANKAN SEKARANG" untuk memulai proses</li>
                  <li><strong>Setelah Kenaikan:</strong> Periksa data siswa & import siswa baru jika ada</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="row mb-4">
        <!-- Total Students -->
        <div class="col-12 mb-4">
          <div class="card" style="background: linear-gradient(135deg, #6f5b9a 0%, #837ab6 100%); border: none; box-shadow: 0 4px 8px rgba(111, 91, 154, 0.3);">
            <div class="card-body text-white py-4">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-2" style="opacity: 0.9; font-size: 14px; font-weight: 500;">Total Siswa</h6>
                  <h2 class="mb-0 fw-bold" style="font-size: 3rem;">{{ $stats['total_students'] }}</h2>
                </div>
                <i class="fas fa-users" style="font-size: 4rem; opacity: 0.25;"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Class Stats -->
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="card h-100" style="background: linear-gradient(135deg, #978cc8 0%, #837ab6 100%); border: none; box-shadow: 0 2px 6px rgba(131, 122, 182, 0.25);">
            <div class="card-body text-white py-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-2" style="opacity: 0.9; font-size: 13px; font-weight: 500;">Kelas X</h6>
                  <h3 class="mb-0 fw-bold" style="font-size: 2.2rem;">{{ $stats['grade_x'] }}</h3>
                </div>
                <i class="fas fa-user-graduate" style="font-size: 2.5rem; opacity: 0.25;"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="card h-100" style="background: linear-gradient(135deg, #a89dd4 0%, #978cc8 100%); border: none; box-shadow: 0 2px 6px rgba(151, 140, 200, 0.25);">
            <div class="card-body text-white py-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-2" style="opacity: 0.9; font-size: 13px; font-weight: 500;">Kelas XI</h6>
                  <h3 class="mb-0 fw-bold" style="font-size: 2.2rem;">{{ $stats['grade_xi'] }}</h3>
                </div>
                <i class="fas fa-user-graduate" style="font-size: 2.5rem; opacity: 0.25;"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="card h-100" style="background: linear-gradient(135deg, #b9aee0 0%, #a89dd4 100%); border: none; box-shadow: 0 2px 6px rgba(168, 157, 212, 0.25);">
            <div class="card-body text-white py-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-2" style="opacity: 0.9; font-size: 13px; font-weight: 500;">Kelas XII</h6>
                  <h3 class="mb-0 fw-bold" style="font-size: 2.2rem;">{{ $stats['grade_xii'] }}</h3>
                </div>
                <i class="fas fa-user-graduate" style="font-size: 2.5rem; opacity: 0.25;"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
          <div class="card h-100" style="background: linear-gradient(135deg, #cabfec 0%, #b9aee0 100%); border: none; box-shadow: 0 2px 6px rgba(185, 174, 224, 0.25);">
            <div class="card-body text-white py-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-2" style="opacity: 0.9; font-size: 13px; font-weight: 500;">Kelas XIII (KA)</h6>
                  <h3 class="mb-0 fw-bold" style="font-size: 2.2rem;">{{ $stats['grade_xiii'] ?? 0 }}</h3>
                </div>
                <i class="fas fa-graduation-cap" style="font-size: 2.5rem; opacity: 0.25;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Repeating Students Warning -->
      @if($stats['repeating_students'] > 0)
      <div class="row mb-4">
        <div class="col-12">
          <div class="alert mb-0" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.15), rgba(255, 193, 7, 0.05)); border-left: 4px solid #ffc107; border-radius: 6px;">
            <div class="d-flex align-items-start">
              <i class="fas fa-exclamation-triangle me-2" style="color: #856404; font-size: 18px; margin-top: 2px;"></i>
              <div>
                <strong style="color: #856404;">Perhatian!</strong> Ada <strong>{{ $stats['repeating_students'] }} siswa</strong> yang ditandai akan mengulang kelas. 
                Mereka tidak akan naik kelas saat proses dijalankan.
                <a href="{{ route('admin.siswa.index') }}" class="alert-link ms-2" style="color: #6f5b9a; text-decoration: underline;">Lihat daftar siswa →</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif

      <!-- Execute Card -->
      <div class="row mb-4">
        <div class="col-lg-8 col-md-10 mx-auto">
          <div class="card" style="background-color: #fff; border: 2px solid #6f5b9a; box-shadow: 0 4px 12px rgba(111, 91, 154, 0.15);">
            <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #6f5b9a 0%, #837ab6 100%); border-bottom: none;">
              <h5 class="mb-0"><i class="fas fa-rocket me-2"></i> Jalankan Kenaikan Tahun</h5>
            </div>
            <div class="card-body p-4">
              <div class="alert alert-warning mb-3" style="background-color: rgba(255, 193, 7, 0.1); border-left: 3px solid #ffc107;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>PERINGATAN:</strong> Proses ini akan mengubah data siswa secara permanen dan tidak dapat dibatalkan.
              </div>
              <form action="{{ route('admin.siswa.year-progression.execute') }}" method="POST" id="yearProgressionForm">
                @csrf
                <button type="button" class="btn btn-lg w-100 py-3" style="background-color: #6f5b9a; color: white; font-weight: 600; font-size: 16px; border: none; box-shadow: 0 4px 8px rgba(111, 91, 154, 0.3); transition: all 0.3s;" onclick="confirmExecution()">
                  <i class="fas fa-exclamation-triangle me-2"></i> JALANKAN SEKARANG
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
          <div class="alert mb-0" style="background: linear-gradient(135deg, rgba(131, 122, 182, 0.15), rgba(131, 122, 182, 0.05)); border-left: 4px solid #837ab6; border-radius: 6px;">
            <h6 class="mb-2" style="color: #6f5b9a;"><i class="fas fa-check-circle me-2"></i> Kenaikan Terakhir</h6>
            <small style="font-size: 13px; color: #555;">
              <strong>Tanggal:</strong> {{ session('last_progression.date') }} | 
              <strong>Oleh:</strong> {{ session('last_progression.user') }}
            </small>
          </div>
        </div>
      </div>
      @endif
    </div>
  </section>

@push('scripts')
<script>
function toggleInstructions() {
    const instructions = document.getElementById('instructions');
    const icon = document.getElementById('instructionIcon');
    
    if (instructions.style.display === 'none') {
        instructions.style.display = 'block';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        instructions.style.display = 'none';
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}

function confirmExecution() {
    const message = 
        'Anda akan menjalankan proses KENAIKAN TAHUN AJARAN.\n\n' +
        'Proses ini akan:\n' +
        '• Menaikkan semua siswa ke kelas berikutnya\n' +
        '• Menghapus data lulusan lama\n' +
        '• TIDAK DAPAT DIBATALKAN!\n\n' +
        'Apakah Anda yakin ingin melanjutkan?';
    
    const title = '⚠️ PERINGATAN PENTING!';
    
    // Use custom showConfirm function
    showConfirm(message, function() {
        // Show loading state
        const btn = document.querySelector('#yearProgressionForm button[type="button"]');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
        
        // Submit the form
        document.getElementById('yearProgressionForm').submit();
    }, title);
    
    return false; // Prevent default button action
}
</script>
@endpush
@endsection