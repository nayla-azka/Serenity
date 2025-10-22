@extends('admin.layouts.layout')

@section('content')
<style>
/* Styling konselor table */
#konselor-table.dataTable th,
#konselor-table.dataTable td {
    border: 1px solid rgb(184, 178, 198) !important;
    padding: 8px 12px;
}

#konselor-table {
    border-collapse: collapse;
    font-size: 14px;
}

#konselor-table thead th {
    background: rgb(131, 122, 182) !important;
    color: white;
    text-align: center;
    vertical-align: middle;
}

/* Hover row */
#konselor-table.dataTable tbody tr:hover td {
    background-color: #f3f2fa !important;
}

/* Toolbar styling */
#custom-toolbar .dataTables_length label,
#custom-toolbar .dataTables_filter label {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: 0;
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

    <h2 class="section-title">Konselor</h2>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card" style="background-color: rgb(222, 220, 240);">
          <div class="card-header d-flex justify-content-between align-items-center p-3">
            <h4>Semua Konselor</h4>
            <div class="card-header-action">
              <a href="{{route('admin.konselor.create')}}" class="dt dt-btn create">
                Buat Baru <i class="fas fa-plus"></i>
              </a>
            </div>
          </div>

          <!-- Toolbar custom -->
          <div id="custom-toolbar" class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div id="konselor-length"></div>
            <div id="konselor-search"></div>
          </div>

          <div class="table-responsive" style="overflow-x:auto;">
            {{ $dataTable->table(['id' => 'konselor-table']) }}
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
      $('#konselor-table').on('init.dt', function () {
        let table = $('#konselor-table').DataTable();

        // Ambil bawaan datatables
        let lengthControl = $('.dataTables_length');
        let filterControl = $('.dataTables_filter');

        // Pindahin ke toolbar custom
        $('#konselor-length').html(lengthControl);
        $('#konselor-search').html(filterControl);

        // Biar rapi
        $('#konselor-length label, #konselor-search label').addClass('mb-0');
      });
    });
  </script>
@endpush
