@extends('admin.layouts.layout')

@section( 'content')
<style>
.btn-serenity{
  color: rgb(248, 246, 255) !important;
  background-color: rgb(131, 122, 182) !important;
}

/* Tambahin grid (garis vertikal & horizontal) */
#banner-table.dataTable th,
#banner-table.dataTable td {
    border: 1px solid rgb(184, 178, 198) !important;
    padding: 8px 12px;
}

/* Styling tabel */
#banner-table {
    border-collapse: collapse;
    font-size: 14px;
}

#banner-table thead th {
    background: rgb(131, 122, 182) !important;
    color: white;
    text-align: center;
    vertical-align: middle;
}

/* Hover efek untuk baris */
#banner-table.dataTable tbody tr:hover td {
    background-color: #f3f2fa !important;
}

/* Foto biar rapi */
#banner-table .photo-img {
    width: 150px;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.078);
    transition: all 0.3s ease; /* animasi halus */
    cursor: pointer;           /* kasih efek bisa di-klik */
}

#banner-table .photo-img:hover {
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.073); /* lebih lembut & menyebar */
    transform: translateY(-2px) scale(1.03);   /* sedikit naik + zoom */
}


/* Deskripsi biar wrap */
#banner-table .wrap-text {
    white-space: normal !important;
    word-wrap: break-word;
    max-width: 400px;
}

/* Tombol aksi */
#banner-table td .btn {
    margin: 2px;
}
#custom-toolbar .dataTables_length label,
#custom-toolbar .dataTables_filter label {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: 0; /* biar rata tengah */
}
.dataTables_scrollHead table thead th {
    background-color: rgb(131, 122, 182) !important;
    color: #fff !important;
    text-align: center;
    vertical-align: middle;
    font-size: 14px;
}
.dataTables_scrollHead table thead th {
    background: rgb(131, 122, 182) !important;
    color: white !important;
}

.dataTables_scrollHeadInner,
.dataTables_scrollHeadInner table {
    width: 100% !important;
    table-layout: fixed;
}

.dataTables_scrollBody table {
    width: 100% !important;
    table-layout: fixed;
}

.dataTables_scrollHeadInner,
.dataTables_scrollHeadInner table,
.dataTables_scrollBody table {
    width: 100% !important;
    table-layout: fixed; /* penting */
}

.dataTables_scrollHeadInner th,
.dataTables_scrollBody td {
    box-sizing: border-box;8
}

</style>
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

        <h2 class="section-title">Banner</h2>
      </div>

    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card" style="background-color: rgb(222, 220, 240);">
            <div class="card-header d-flex justify-content-between align-items-center p-3">
              <h5>Data Banner</h5>
              <div class="card-header-action">
                <a href="{{route('admin.banner.create')}}" class="dt-btn create">Buat Baru<i class="fas fa-plus"></i></a>
              </div>
            </div>
              <div id="toolbar" class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div id="banner-length"></div>
                <div id="banner-search"></div>
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
          $('#banner-table').on('init.dt', function () {
              let table = $('#banner-table').DataTable();

              // Ambil elemen bawaan yg dibuat DataTables
              let lengthControl = $('.dataTables_length');
              let filterControl = $('.dataTables_filter');

              // Pindahin ke toolbar custom
              $('#banner-length').html(lengthControl);
              $('#banner-search').html(filterControl);

              // Styling biar sejajar
              $('#banner-length label, #banner-search label').addClass('mb-0');

              // Klik foto → redirect ke edit
              document.addEventListener("click", function(e) {
                  if (e.target.classList.contains("photo-img")) {
                      let id = e.target.getAttribute("data-id");
                      window.location.href = "/admin/banner/" + id + "/edit";
                  }
              });
          });
      });
    </script>
@endpush
