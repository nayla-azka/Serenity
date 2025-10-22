@extends('admin.layouts.layout')

@push('styles')
<style>
/* Styling tabel */
#artikel-table {
    border-collapse: collapse;
    font-size: 14px;
}

#artikel-table thead th {
    background: rgb(131, 122, 182);
    color: white;
    text-align: center;
    vertical-align: middle;
}

/* Foto biar rapi */
#artikel-table .photo-img {
    width: 150px;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}

/* Deskripsi biar wrap */
#artikel-table .wrap-text {
    white-space: normal !important;
    word-wrap: break-word;
    max-width: 400px;
}

/* Tombol aksi */
#artikel-table td .btn {
    margin: 2px;
}
/* batasi semua gambar di dalam kolom konten artikel */
#artikel-table td img {
    max-width: 250px;   /* ubah sesuai kebutuhan */
    max-height: 150px;
    height: auto;
    width: auto;
    object-fit: cover;  /* biar rapi */
    border-radius: 6px; /* opsional, biar halus */
}


</style>
@endpush

@section('content')
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
        <h2 class="section-title">Artikel</h2>
    </div>

    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card" style="background-color: rgb(222, 220, 240);">
            <div class="card-header d-flex justify-content-between align-items-center p-3">
              <h5>Data Artikel</h5>
              <div class="card-header-action">
                <a href="{{ route('admin.artikel.create') }}" class="dt-btn create">Buat Baru <i class="fas fa-plus"></i></a>
              </div>
            </div>

            <!-- Toolbar custom -->
            <div id="toolbar" class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div id="artikel-length"></div>
                <div id="artikel-search"></div>
            </div>

            <!-- Table -->
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
      document.addEventListener("click", function(e) {
                  if (e.target.classList.contains("photo-img")) {
                      let id = e.target.getAttribute("data-id");
                      window.location.href = "/admin/artikel/" + id + "/edit";
                  }
              });

      document.addEventListener("DOMContentLoaded", function () {
          // pindahin search & length ke toolbar
          $('#artikel-table').on('init.dt', function () {
              let lengthControl = $('.dataTables_length');
              let filterControl = $('.dataTables_filter');

              $('#artikel-length').append(lengthControl);
              $('#artikel-search').append(filterControl);

              $('#artikel-length label, #artikel-search label').addClass('mb-0');
          });
      });
    </script>
@endpush
