@extends('admin.layouts.layout')

@section( 'content')
<section class="section">
    <div class="section-header text-black">
        <div class="section-header-back">
          <a href="{{ url('admin/dashboard') }}" class="btn-back">
            <span class="btn-back__icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" height="20" width="20">
                <path d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z" fill="#fff"/>
                <path d="m237.248 512 265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z" fill="#fff"/>
              </svg>
            </span>
          </a>
        </div>
        <h2 class="section-title">Siswa</h2>
    </div>

    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card" style="background-color: rgb(222, 220, 240);">
            <div class="card-header d-flex justify-content-between align-items-center p-3">
              <h4>Semua Siswa</h4>
              <div class="card-header-action">
                <a href="{{route('admin.siswa.create')}}" class="dt-btn create">Buat Baru<i class="fas fa-plus"></i></a>
              </div>
            </div>
                <div id="toolbar" class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div id="siswa-length"></div>
                    <div id="siswa-search"></div>
                </div>
            <div class="table-responsive" style="overflow-x:auto;">
                {{ $dataTable->table() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Tunggu DataTables siap
    $('#siswa-table').on('init.dt', function () {
        let table = $('#siswa-table').DataTable();

        // Ambil elemen bawaan DataTables
        let lengthControl = $('.dataTables_length');
        let filterControl = $('.dataTables_filter');

        // Pindahkan ke toolbar custom
        $('#siswa-length').html(lengthControl);
        $('#siswa-search').html(filterControl);

        // Styling label biar rata tengah
        $('#siswa-length label, #siswa-search label').addClass('mb-0');
    });
});
</script>

@endpush
