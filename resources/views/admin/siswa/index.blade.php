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
        <h2 class="section-title">Manajemen Siswa</h2>
    </div>

    <div class="section-body">
      <div class="row">
        <div class="col-12">
          
          <!-- Bulk Action Card - Status Kenaikan Kelas -->
          <div class="card mb-3" style="background-color: rgb(222, 220, 240); border-left: 4px solid #6f42c1;">
            <div class="card-body">
              <h6 class="mb-2"><i class="fas fa-tasks"></i> Aksi Massal - Status Kenaikan Kelas</h6>
              <form id="bulkRepeatForm" method="POST" action="{{ route('admin.siswa.bulk.repeat') }}">
                @csrf
                <div class="row align-items-end g-2">
                  <div class="col-md-3">
                    <label class="form-label mb-1" style="font-size: 13px; font-weight: 600;">Siswa Terpilih</label>
                    <div id="selectedCount" class="badge bg-primary" style="font-size: 14px; padding: 8px 12px; display: block;">
                      0 siswa dipilih
                    </div>
                  </div>
                  <div class="col-md-5">
                    <label class="form-label mb-1" style="font-size: 13px; font-weight: 600;">Status Kenaikan</label>
                    <select name="repeat_grade" class="form-control input-style" required>
                      <option value="1">🔄 Tandai Mengulang Kelas</option>
                      <option value="0">✅ Tandai Naik Kelas Normal</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <button type="submit" class="dt-btn w-100" id="bulkSubmit" disabled style="background-color: #6f42c1; color: #ffffff; font-weight: 600;">
                      <i class="fas fa-check me-2"></i> Terapkan
                    </button>
                  </div>
                </div>
              </form>
              <small class="text-muted d-block mt-2" style="font-size: 12px;">
                <i class="fas fa-info-circle"></i> 
                Pilih siswa dengan checkbox, lalu tandai status. Siswa "Mengulang" tidak akan naik kelas otomatis.
              </small>
            </div>
          </div>

          <!-- Main Table Card -->
          <div class="card" style="background-color: rgb(222, 220, 240);">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2" style="padding: 1.25rem;">
              <h5 class="mb-0"><i class="fas fa-users"></i> Daftar Siswa</h5>
              <div class="d-flex gap-2 flex-wrap">
                <!-- Kenaikan Tahun Button -->
                <a href="{{ route('admin.siswa.year-progression') }}" class="dt-btn text-white" style="background-color: #6f42c1;">
                  <i class="fas fa-calendar-alt me-2"></i> Kenaikan Tahun
                </a>
                
                @if(auth()->user()->isKonselor())
                <a href="{{route('admin.siswa.export.all')}}" class="dt-btn text-white" style="background-color: #dc3545;" title="Export dengan Password (Konselor Only)">
                  <i class="fas fa-file-excel me-2"></i> Export + Password
                </a>
                @endif
                
                <a href="{{route('admin.siswa.import')}}" class="dt-btn text-white" style="background-color: #28a745;">
                  <i class="fas fa-file-import me-2"></i> Import
                </a>
                
                <a href="{{route('admin.siswa.create')}}" class="dt-btn create">
                  <i class="fas fa-plus me-2"></i> Buat Baru
                </a>
              </div>
            </div>

            <div class="card-body">
              <!-- Toolbar -->
              <div id="toolbar" class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                  <div id="siswa-length"></div>
                  <div id="siswa-search"></div>
              </div>

              <!-- Table -->
              <div class="table-responsive" style="overflow-x:auto;">
                  {{ $dataTable->table() }}
              </div>
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
let selectedStudents = new Set();

document.addEventListener("DOMContentLoaded", function () {
    // Setup DataTable toolbar
    $('#siswa-table').on('init.dt', function () {
        let table = $('#siswa-table').DataTable();
        
        // Move controls to toolbar
        let lengthControl = $('.dataTables_length');
        let filterControl = $('.dataTables_filter');
        $('#siswa-length').html(lengthControl);
        $('#siswa-search').html(filterControl);
        $('#siswa-length label, #siswa-search label').addClass('mb-0');
        
        // Add checkbox to header
        $('#siswa-table thead tr th:first').before('<th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAll"></th>');
    });
    
    // Add checkboxes to each row
    $('#siswa-table').on('draw.dt', function() {
        $('#siswa-table tbody tr').each(function() {
            const studentId = $(this).attr('id');
            
            if (studentId) {
                const checkbox = `<td style="width: 40px; text-align: center;"><input type="checkbox" class="student-checkbox" value="${studentId}"></td>`;
                $(this).find('td:first').before(checkbox);
                
                // Restore checked state
                if (selectedStudents.has(studentId)) {
                    $(this).find('.student-checkbox').prop('checked', true);
                }
            }
        });
        
        updateSelectedCount();
    });
    
    // Select All
    $(document).on('change', '#selectAll', function() {
        const isChecked = $(this).is(':checked');
        $('.student-checkbox').each(function() {
            $(this).prop('checked', isChecked);
            const studentId = $(this).val();
            if (isChecked) {
                selectedStudents.add(studentId);
            } else {
                selectedStudents.delete(studentId);
            }
        });
        updateSelectedCount();
    });
    
    // Individual checkbox
    $(document).on('change', '.student-checkbox', function() {
        const studentId = $(this).val();
        if ($(this).is(':checked')) {
            selectedStudents.add(studentId);
        } else {
            selectedStudents.delete(studentId);
            $('#selectAll').prop('checked', false);
        }
        updateSelectedCount();
    });
    
    // Update count
    function updateSelectedCount() {
        const count = selectedStudents.size;
        $('#selectedCount').text(`${count} siswa dipilih`);
        $('#bulkSubmit').prop('disabled', count === 0);
    }
    
    // Form submission
    $('#bulkRepeatForm').on('submit', function(e) {
        e.preventDefault();
        
        if (selectedStudents.size === 0) {
            alert('Pilih minimal 1 siswa!');
            return false;
        }
        
        // Remove old inputs
        $(this).find('input[name="student_ids[]"]').remove();
        
        // Add selected IDs
        selectedStudents.forEach(function(studentId) {
            $('<input>').attr({
                type: 'hidden',
                name: 'student_ids[]',
                value: studentId
            }).appendTo('#bulkRepeatForm');
        });
        
        // Confirm
        const action = $('select[name="repeat_grade"]').val() == '1' ? 'MENGULANG KELAS' : 'NAIK KELAS NORMAL';
        if (confirm(`Tandai ${selectedStudents.size} siswa sebagai ${action}?\n\nSiswa yang mengulang tidak akan naik kelas otomatis.`)) {
            this.submit();
        }
    });
});
</script>
@endpush