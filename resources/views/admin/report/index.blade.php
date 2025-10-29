@extends('admin.layouts.layout')

@section( 'content')
<style>
    .dt-btn.report {
    color: #837ab6;          /* Warna teks default */
    background-color: transparent;
    border-radius: 0.375rem; /* biar agak bulat */
    transition: all 0.2s ease;
}

.dt-btn.report:hover {
    background-color: #f2eefc; /* background halus waktu hover */
    color: #6b61a4;
}

.dt-btn.report.active {
    color: #faedff;          /* teks jadi putih */
    background-color: #837ab6;
}

.dt-btn.report.active:hover {
    background-color: #6b61a4; /* hover di active tab */
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
        <h2 class="section-title">Report</h2>
    </div>

    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
               <ul class="nav nav-pills gap-2" id="report-tabs">
                    <li class="nav-item">
                        <a class="nav-link dt-btn report active" data-status="all" href="#">All ({{ $counts['all'] }})</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link dt-btn report" data-status="Pending" href="#">Pending ({{ $counts['Pending'] }})</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link dt-btn report" data-status="Diterima" href="#">Diterima ({{ $counts['Diterima'] }})</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link dt-btn report" data-status="Ditolak" href="#">Ditolak ({{ $counts['Ditolak'] }})</a>
                    </li>
                </ul>
            </div>
            <div class="table-responsive" style="overflow-x:auto;">
                {!! $dataTable->table() !!}
                <button id="delete-selected" class="dt dt-btn delete mt-2" style="margin-left: 20px; margin-bottom: 10px;">Delete Selected</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<!-- Modal Preview Komentar -->
<div class="modal fade" id="commentPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-2xl shadow-lg">
      <div class="modal-header">
        <h5 class="modal-title">Preview Komentar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>User:</strong> <span id="preview-user"></span></p>
        <p><strong>Artikel:</strong> <span id="preview-article"></span></p>
        <p><strong>Tanggal:</strong> <span id="preview-date"></span></p>
        <hr>
        <p id="preview-comment-text" class="mt-3"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="dt dt-btn create" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Status Update with Notes -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Ubah status laporan menjadi: <strong id="status-label"></strong></p>
                <div class="mb-3">
                    <label for="adminNotes" class="form-label">Catatan Admin (Opsional)</label>
                    <textarea class="form-control" id="adminNotes" rows="3"
                        placeholder="Tambahkan catatan untuk reporter dan pemilik komentar..."></textarea>
                    <small class="text-muted">Catatan ini akan dikirim dalam notifikasi kepada pengguna terkait.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="dt dt-btn delete" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="dt dt-btn edit" id="confirm-status-update">
                    Update Status
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Report Details -->
<div class="modal fade" id="reportDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="details-modal-title">Detail Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="details-modal-body">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer" id="details-modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{ $dataTable->scripts(attributes: ['type' => 'module']) }}
<script>
    // Global variables for status update
let currentReportId = null;
let currentNewStatus = null;

$(document).on('click', '#report-tabs .nav-link', function(e) {
    e.preventDefault();

    $('#report-tabs .nav-link').removeClass('active');
    $(this).addClass('active');

    $('#report-table').DataTable().ajax.reload();
});

$(document).on('click', '.preview-comment', function (e) {
    e.preventDefault();

    $('#preview-user').text($(this).attr('data-user'));
    $('#preview-article').text($(this).attr('data-article'));
    $('#preview-date').text($(this).attr('data-date'));
    $('#preview-comment-text').html($(this).attr('data-comment')); // use html if you want formatting

    const modal = new bootstrap.Modal(document.getElementById('commentPreviewModal'));
    modal.show();
});

// Enhanced status update with modal and notes
$(document).on('click', '.update-status', function () {
    currentReportId = $(this).data('id');
    currentNewStatus = $(this).data('status');

    const statusLabels = {
        'Pending': 'Pending',
        'Diterima': 'Diterima',
        'Ditolak': 'Ditolak'
    };

    $('#status-label').text(statusLabels[currentNewStatus] || currentNewStatus);
    $('#adminNotes').val(''); // Clear previous notes

    const modal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
    modal.show();
});

// Confirm status update with notes
$(document).on('click', '#confirm-status-update', function() {
    const adminNotes = $('#adminNotes').val().trim();
    const button = $(this);
    const originalText = button.html();

    // Validate required data
    if (!currentReportId || !currentNewStatus) {
        showToast('Data tidak valid', 'danger');
        return;
    }

    // Show loading state
    button.html('<i class="fas fa-spinner fa-spin"></i> Memperbarui...').prop('disabled', true);

    $.ajax({
        url: "{{ route('admin.report.updateStatus') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: currentReportId,
            status: currentNewStatus,
            admin_notes: adminNotes || null // Send null if empty
        },
        timeout: 10000, // 10 second timeout
        success: function(response) {
            console.log('Update response:', response); // Debug log

            if (response && response.status === 'success') {
                $('#statusUpdateModal').modal('hide');
                showToast(response.message || 'Status berhasil diperbarui', 'success');

                if (response.counts) {
                    updateTabCounts(response.counts);
                }

                // Reload the table after a short delay
                setTimeout(function() {
                    $('#report-table').DataTable().ajax.reload();
                }, 500);
            } else {
                showToast(response?.message || 'Response tidak valid', 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', {
                status: xhr.status,
                responseText: xhr.responseText,
                statusText: status,
                error: error
            }); // Debug log

            let errorMessage = 'Terjadi kesalahan saat memperbarui status';

            if (xhr.status === 422) {
                // Validation error
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || 'Data tidak valid';
                } catch (e) {
                    errorMessage = 'Validasi gagal';
                }
            } else if (xhr.status === 500) {
                // Server error
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || 'Kesalahan server';
                } catch (e) {
                    errorMessage = 'Kesalahan server internal';
                }
            } else if (xhr.status === 0) {
                errorMessage = 'Koneksi terputus atau timeout';
            } else {
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || `HTTP Error ${xhr.status}`;
                } catch (e) {
                    errorMessage = `HTTP Error ${xhr.status}: ${xhr.statusText}`;
                }
            }

            showToast(errorMessage, 'danger');
        },
        complete: function() {
            button.html(originalText).prop('disabled', false);
        }
    });
});

// View report details
$(document).on('click', '.view-details', function() {
    const reportId = $(this).data('id');
    const reason = $(this).data('reason');
    const notes = $(this).data('notes');
    const status = $(this).data('status');

    showReportDetailsModal(reportId, reason, notes, status);
});

function updateTabCounts(counts) {
    $('#report-tabs .nav-link[data-status="all"]').text(`All (${counts.all})`);
    $('#report-tabs .nav-link[data-status="Pending"]').text(`Pending (${counts.Pending})`);
    $('#report-tabs .nav-link[data-status="Diterima"]').text(`Diterima (${counts.Diterima})`);
    $('#report-tabs .nav-link[data-status="Ditolak"]').text(`Ditolak (${counts.Ditolak})`);
}

function showReportDetailsModal(reportId, reason, notes, status) {
    const statusLabels = {
        'Pending': '<span class="badge bg-warning">Pending</span>',
        'Diterima': '<span class="badge bg-success">Diterima</span>',
        'Ditolak': '<span class="badge bg-danger">Ditolak</span>'
    };

    const statusBadge = statusLabels[status] || status;

    $('#details-modal-title').text(`Detail Laporan #${reportId}`);

    let bodyContent = `
        <div class="mb-3">
            <label class="form-label fw-bold">Status</label>
            <div>${statusBadge}</div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Alasan Laporan</label>
            <div class="border p-3 bg-light rounded">
                ${reason || 'Tidak ada alasan'}
            </div>
        </div>
    `;

    if (notes) {
        bodyContent += `
            <div class="mb-3">
                <label class="form-label fw-bold">Catatan Admin</label>
                <div class="border p-3 bg-info bg-opacity-10 rounded">
                    ${notes}
                </div>
            </div>
        `;
    }

    if (status === 'Pending') {
        bodyContent += `
            <div class="mb-3">
                <label for="newAdminNotes" class="form-label fw-bold">Tambah/Edit Catatan Admin</label>
                <textarea class="form-control" id="newAdminNotes" rows="3"
                    placeholder="Tambahkan catatan...">${notes || ''}</textarea>
            </div>
        `;
    }

    $('#details-modal-body').html(bodyContent);

    let footerContent = '<button type="button" class="dt dt-btn create" data-bs-dismiss="modal">Tutup</button>';

    if (status === 'Pending') {
        footerContent += `
            <button type="button" class="btn btn-success"
                    onclick="quickUpdateStatus(${reportId}, 'Diterima')">
                <i class="fas fa-check"></i> Terima
            </button>
            <button type="button" class="btn btn-danger"
                    onclick="quickUpdateStatus(${reportId}, 'Ditolak')">
                <i class="fas fa-times"></i> Tolak
            </button>
        `;
    }

    $('#details-modal-footer').html(footerContent);

    const modal = new bootstrap.Modal(document.getElementById('reportDetailsModal'));
    modal.show();
}

// Quick status update from details modal
function quickUpdateStatus(reportId, newStatus) {
    const adminNotes = $('#newAdminNotes').val()?.trim() || '';

    // Find and disable buttons to prevent double-clicks
    const buttons = $('#details-modal-footer button').not('[data-bs-dismiss="modal"]');
    buttons.prop('disabled', true);

    $.ajax({
        url: "{{ route('admin.report.updateStatus') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: reportId,
            status: newStatus,
            admin_notes: adminNotes || null
        },
        timeout: 10000,
        success: function(response) {
            console.log('Quick update response:', response); // Debug log

            if (response && response.status === 'success') {
                $('#reportDetailsModal').modal('hide');
                showToast(response.message || 'Status berhasil diperbarui', 'success');

                if (response.counts) {
                    updateTabCounts(response.counts);
                }

                setTimeout(function() {
                    $('#report-table').DataTable().ajax.reload();
                }, 500);
            } else {
                showToast(response?.message || 'Response tidak valid', 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Quick Update AJAX Error:', {
                status: xhr.status,
                responseText: xhr.responseText,
                statusText: status,
                error: error
            });

            let errorMessage = 'Terjadi kesalahan saat memperbarui status';

            try {
                const response = JSON.parse(xhr.responseText);
                errorMessage = response.message || errorMessage;
            } catch (e) {
                if (xhr.status === 0) {
                    errorMessage = 'Koneksi terputus atau timeout';
                } else {
                    errorMessage = `HTTP Error ${xhr.status}`;
                }
            }

            showToast(errorMessage, 'danger');
        },
        complete: function() {
            buttons.prop('disabled', false);
        }
    });
}

$(document).on('click', '#select-all', function() {
    $('.select-row').prop('checked', this.checked);
});

$(document).on('click', '#delete-selected', function() {
    let ids = [];
    $('.select-row:checked').each(function(){
        ids.push($(this).val());
    });

    if(ids.length === 0){
        showToast('Tidak ada laporan yang dipilih', 'warning');
        return;
    }
    const button = $(this);
    const originalText = button.html();

    showConfirm(`Yakin ingin menghapus laporan yang dipilih??`, function () {
        button.html('<i class="fas fa-spinner fa-spin"></i> Menghapus...').prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.report.bulkDelete') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                ids: ids
            },
            timeout: 15000,
            success: function(response){
                console.log('Bulk delete response:', response);

                if (response && response.status === 'success') {
                    showToast(response.message || 'Laporan berhasil dihapus', 'success');
                    $('#report-table').DataTable().ajax.reload();
                    $('#select-all').prop('checked', false);

                    // 🔥 update tab counts dynamically
                    if (response.counts) {
                        updateTabCounts(response.counts);
                    }
                } else {
                    showToast(response?.message || 'Response tidak valid', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('Bulk Delete AJAX Error:', xhr);

                let errorMessage = 'Terjadi kesalahan saat menghapus laporan';

                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || errorMessage;
                } catch (e) {
                    if (xhr.status === 0) {
                        errorMessage = 'Koneksi terputus atau timeout';
                    }
                }

                showToast(errorMessage, 'danger');
            },
            complete: function() {
                button.html(originalText).prop('disabled', false);
            }
        });
    });
});

// fungsi toast
function showToast(message, type = "success") {
    let toast = document.createElement("div");
    toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
    toast.role = "alert";
    toast.style.zIndex = "9999";
    toast.innerHTML = `<div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>`;
    document.body.appendChild(toast);

    let bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 5000
    });
    bsToast.show();

    toast.addEventListener("hidden.bs.toast", () => {
        toast.remove();
    });
}

// Make quickUpdateStatus globally available
window.quickUpdateStatus = quickUpdateStatus;

// 👇 panggil berdasarkan session flash
document.addEventListener("DOMContentLoaded", function () {
    @if(session('success'))
        showToast("{{ session('success') }}", "success");
    @endif

    @if(session('error'))
        showToast("{{ session('error') }}", "danger");
    @endif

     @if($highlightReport)
        let reportId = {{ $highlightReport }};

        $('#report-table').on('draw.dt', function () {
            let row = $('#'+reportId);
            if (row.length) {
                row.addClass('table-warning'); // bootstrap highlight
                $('html, body').animate({
                    scrollTop: row.offset().top - 150
                }, 600);
            }
        });
    @endif
});
</script>

@endpush
