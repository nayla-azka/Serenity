@extends('admin.layouts.layout')

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
        <h2 class="section-title">User</h2>
    </div>

    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card" style="background-color: rgb(222, 220, 240);">
            <div class="card-header d-flex justify-content-between align-items-center p-3">
              <h4>Data User</h4>
              <div class="card-header-action">
                <a href="{{route('admin.user.create')}}" class="dt dt-btn create">Buat Baru<i class="fas fa-plus"></i></a>
              </div>
            </div>

            <!-- Toolbar custom -->
            <div id="toolbar" class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div id="user-length"></div>
                <div id="user-search"></div>
            </div>

            <div class="table-responsive" style="overflow-x:auto;">
                {{ $dataTable->table(['id' => 'user-table']) }}
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
          $('#user-table').on('init.dt', function () {
              let table = $('#user-table').DataTable();

              // ambil elemen bawaan DataTables
              let lengthControl = $('.dataTables_length');
              let filterControl = $('.dataTables_filter');

              // pindahkan ke toolbar custom
              $('#user-length').html(lengthControl);
              $('#user-search').html(filterControl);

              // biar rata tengah
              $('#user-length label, #user-search label').addClass('mb-0');
          });
      });
    </script>
@endpush
