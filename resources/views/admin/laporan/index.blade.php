@extends('admin.layouts.layout')

@push('styles')
  <style>
    /* ✅ Scale keseluruhan content jadi 67% */
    .section {
      zoom: 0.9; /* ngecilin jadi 90% */
    }

    .laporan-card.selected {
      border: 2px solid #007bff;
      background-color: #e0dbff;
    }
    #laporan-indicators .indicator {
      display: inline-block;
      width: 10px;
      height: 10px;
      background: #ccc;
      border-radius: 50%;
      cursor: pointer;
      transition: background 0.3s;
    }
    #laporan-indicators .indicator.active {
      background: #007bff;
    }
  </style>
@endpush

@section('content')
<section class="section">
    <div class="section-header text-black mb-4">
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
        <h2 class="ms-2">Laporan Baru</h2>
    </div>

    <div class="section-body" style="zoom: 0.9;">
      <div class="column">
        <div class="col-12">
          <div class="mb-4">
            <div class="card-body">
              <div id="laporan-highlight-container">
                  @include('admin.laporan.highlight', ['laporanDiproses' => $laporanDiproses])
              </div>

              <!-- Modal detail -->
              <div class="modal fade" id="laporanModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">Detail Laporan</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                      <!-- Topic & Status -->
                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 id="modal-topic" class="mb-0"></h3>
                        <span id="modal-status" class="badge"></span>
                      </div>

                      <!-- Info tambahan -->
                      <small class="text-muted d-block mb-2" id="modal-info"></small>
                      <p><strong>Tanggal:</strong> <span id="modal-date"></span></p>
                      <p><strong>Tempat:</strong> <span id="modal-place"></span></p>

                      <!-- Kronologi -->
                      <p><strong>Kronologi:</strong></p>
                      <div id="modal-chronology"></div>
                    </div>

                    <div class="modal-footer" id="modal-actions">
                      <button id="btn-diproses" class="dt dt-btn edit">Proses</button>
                      <button id="btn-ditolak" class="dt dt-btn delete">Tolak</button>
                      <button id="btn-selesai" class="dt dt-btn terima">Selesai</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card" style="background-color: rgb(250, 250, 255)">
            <div class="card-header">
              <h4>Laporan Selesai</h4>
            </div>
            <div class="card-body">
                {!! $dataTable->table() !!}
                <button id="delete-selected" class="dt dt-btn delete mt-2"> <i class="fas fa-trash me-1"></i>Delete Selected</button>
                <button id="restore-selected" class="dt dt-btn edit mt-2"><i class="bi bi-gear-fill me-1"></i>Kembalikan ke Diproses</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

<script>
document.addEventListener("DOMContentLoaded", function() {
    let laporanId = "{{ request('highlight') }}";
    if (!laporanId) return;

    let card = document.getElementById(`laporan-card-${laporanId}`);
    if (card) {
        // scroll ke card biar user tahu
        card.scrollIntoView({ behavior: "smooth", block: "center" });

        // highlight card sebentar
        card.classList.add("border", "border-primary", "shadow-lg");
        setTimeout(() => {
            card.classList.remove("border", "border-primary", "shadow-lg");
        }, 3000);

        // buka modal setelah sedikit delay
        setTimeout(() => openLaporanModal(card), 800);
    }
});
</script>


@push('scripts')
{{ $dataTable->scripts(attributes: ['type' => 'module']) }}
<script>
let currentIndex = 0;
let cards = $(".laporan-highlight-card");

$(document).ready(function() {
    showCard(0);
});

document.addEventListener("DOMContentLoaded", function() {
    let laporanId = "{{ request('highlight') }}";
    if (!laporanId) return;

    let card = document.getElementById(`laporan-card-${laporanId}`);
    if (card) {
        card.scrollIntoView({ behavior: "smooth", block: "center" });

        card.classList.add("border", "border-primary", "shadow-lg");
        setTimeout(() => {
            card.classList.remove("border", "border-primary", "shadow-lg");
        }, 3000);

        // delay slightly so scroll finishes
        setTimeout(() => openLaporanModal(card), 800);
    }
});


function reloadHighlight() {
    $("#laporan-highlight-container").load(location.href + " #laporan-highlight-container > *", function() {
        // re-init carousel state
        cards = $(".laporan-highlight-card");
        currentIndex = 0;
        showCard(0);
    });
}

function showCard(index) {
    cards = $(".laporan-highlight-card");
    cards.addClass("d-none");

    if (cards.length > 0) {
        if (index >= cards.length) index = cards.length - 1;
        if (index < 0) index = 0;
        currentIndex = index;

        $(cards[currentIndex]).removeClass("d-none");

        // show prev/next
        $("#prev-card, #next-card").removeClass("d-none");
        $("#prev-card").prop("disabled", currentIndex === 0);
        $("#next-card").prop("disabled", currentIndex === cards.length - 1);

        // generate indicators if not exist
        if ($("#laporan-indicators").children().length !== cards.length) {
            $("#laporan-indicators").empty();
            cards.each(function(i) {
                $("#laporan-indicators").append(
                    `<span class="indicator mx-1" data-index="${i}"></span>`
                );
            });
        }

        // update active indicator
        $("#laporan-indicators .indicator").removeClass("active");
        $(`#laporan-indicators .indicator[data-index=${currentIndex}]`).addClass("active");

    } else {
        $("#laporan-highlight-card").html(`
            <div id="no-laporan" class="p-5 text-center text-muted border rounded">
                <i class="fas fa-inbox fa-2x mb-3"></i>
                <p class="mb-0">Tidak ada laporan menunggu atau diproses</p>
            </div>
        `);
        $("#prev-card, #next-card").addClass("d-none");
        $("#laporan-indicators").empty();
    }
}

// allow clicking indicator
$(document).on("click", "#laporan-indicators .indicator", function() {
    let idx = $(this).data("index");
    showCard(idx);
});

// tombol navigasi next/prev
$(document).on("click", "#next-card", function() {
    if (currentIndex < cards.length - 1) {
        showCard(currentIndex + 1);
    }
});

$(document).on("click", "#prev-card", function() {
    if (currentIndex > 0) {
        showCard(currentIndex - 1);
    }
});

function openLaporanModal(card) {
    let id = card.dataset.id;
    let topic = card.dataset.topic;
    let pengirim = card.dataset.sender;
    let created_at = card.dataset.created_at;
    let tanggal = card.dataset.date;
    let tempat = card.dataset.place;
    let chronology = card.dataset.chronology;
    let status = card.dataset.status;

    $("#modal-topic").text(topic);
    $("#modal-info").text(`${pengirim} · ${created_at}`);
    $("#modal-place").text(tempat);
    $("#modal-date").text(tanggal);
    $("#modal-chronology").text(chronology);

    const modalBadge = document.getElementById("modal-status");
    modalBadge.className = "badge " + (
        status === "Selesai" ? "bg-success" :
        status === "Diproses" ? "bg-primary" :
        status === "Menunggu" ? "bg-warning" : "bg-danger"
    );
    modalBadge.textContent = status;

    $("#btn-diproses, #btn-ditolak, #btn-selesai").addClass("d-none");
    if (status === "Menunggu") {
        $("#btn-diproses, #btn-ditolak").removeClass("d-none");
    } else if (status === "Diproses") {
        $("#btn-selesai, #btn-ditolak").removeClass("d-none");
    }

    $("#laporanModal").data("id", id).modal("show");
}


// klik card → buka modal detail
$(document).on("click", ".laporan-highlight-card", function() {
    openLaporanModal(this);
});

// helper: refresh prev/next button state
function updateNavButtons() {
    cards = $(".laporan-highlight-card");
    if (cards.length === 0) {
        $("#prevBtn, #nextBtn").hide();
    } else {
        $("#prevBtn, #nextBtn").show();
        $("#prevBtn").prop("disabled", currentIndex === 0);
        $("#nextBtn").prop("disabled", currentIndex === cards.length - 1);
    }
}

//  Update status laporan
function updateStatus(id, status) {
    showConfirm(`Yakin ingin mengupdate status laporan ini?`, function () {
        let url = "{{ route('admin.laporan.updateStatus', ['id' => ':id']) }}".replace(':id', id);
        fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            body: JSON.stringify({ status }),
        })
        .then(res => {
            if (!res.ok) throw new Error("Network error");
            return res.json();
        })
        .then(data => {
            // 🔄 update badge modal
            const modalBadge = document.getElementById("modal-status");
            modalBadge.className = "badge " + (
                status === "Selesai" ? "bg-success" :
                status === "Diproses" ? "bg-primary" :
                status === "Menunggu" ? "bg-warning" : "bg-danger"
            );
            modalBadge.textContent = status;

            // 🔄 update card badge in highlight area
            let card = $(`.laporan-highlight-card[data-id="${id}"]`);
            let badge = $(card).find(".badge");
            badge.removeClass("bg-success bg-primary bg-warning bg-danger").addClass(
                status === "Selesai" ? "bg-success" :
                status === "Diproses" ? "bg-primary" :
                status === "Menunggu" ? "bg-warning" : "bg-danger"
            );
            badge.text(status);

             // 🔄 handle card movement/removal
            if (status === "Selesai" || status === "Ditolak") {
                $(card).fadeOut(300, function () {
                    $(this).remove();
                    cards = $(".laporan-highlight-card");
                    if (cards.length > 0) {
                        if (currentIndex >= cards.length) currentIndex = cards.length - 1;
                        showCard(currentIndex);
                    } else {
                        $("#laporan-highlight-card").html(`
                            <div id="no-laporan" class="p-5 text-center text-muted border rounded">
                                <i class="fas fa-inbox fa-2x mb-3"></i>
                                <p class="mb-0">Tidak ada laporan menunggu atau diproses</p>
                            </div>
                        `);
                    }
                    updateNavButtons();
                });
            } else if (status === "Diproses") {
                $(card).appendTo("#laporan-highlight-card");
                showCard($(".laporan-highlight-card").length - 1);
                updateNavButtons();
            }

            // 🔄 reload datatable
            if ($.fn.DataTable.isDataTable("#laporan-table")) {
                $('#laporan-table').DataTable().ajax.reload();
            }

            // close modal + toast
            $("#laporanModal").modal("hide");
            showToast(`Status updated to ${status}`, "success");

            // 🔄 reload highlight cards (carousel + indicators + buttons)
            $("#laporan-highlight-container").load(location.href + " #laporan-highlight-container > *", function () {
                cards = $(".laporan-highlight-card");
                currentIndex = 0;
                showCard(currentIndex); // re-initialize carousel
        })

      })
        .catch(err => {
            console.error(err);
            showToast("Failed to update status", "danger");
        });
    });
}

// 🔘 Tombol update status
$("#btn-diproses").click(() => {
    let id = $("#laporanModal").data("id");
    updateStatus(id, "Diproses");
});
$("#btn-ditolak").click(() => {
    let id = $("#laporanModal").data("id");
    updateStatus(id, "Ditolak");
});
$("#btn-selesai").click(() => {
    let id = $("#laporanModal").data("id");
    updateStatus(id, "Selesai");
});

// ✅ Toggle select datatable card
$(document).on("click", ".laporan-card", function () {
    $(this).toggleClass("selected");
});

// ✅ Generic bulk handler (pakai modal confirm)
function handleBulkAction(url, confirmMessage, successMessage, errorMessage) {
    let selected = $(".laporan-card.selected");
    if (selected.length === 0) {
        showToast("Pilih laporan terlebih dahulu", "danger");
        return;
    }
    let ids = selected.map(function () { return $(this).data("id"); }).get();

    showConfirm(confirmMessage, function () {
        fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            body: JSON.stringify({ ids }),
        })
        .then(async res => {
            const contentType = res.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                return res.json();
            } else {
                throw new Error("SESSION_EXPIRED");
            }
        })
        .then(data => {
            if (data.status === "success" || data.success) {
                $('#laporan-table').DataTable().ajax.reload();
                showToast(successMessage, "success");
                // 🔄 reload highlight
                $("#laporan-highlight-container").load(location.href + " #laporan-highlight-container > *", function () {
                    cards = $(".laporan-highlight-card");
                    currentIndex = 0;
                    showCard(currentIndex);
                })
            } else {
                showToast(data.message || errorMessage, "danger");
            }
        })
        .catch(err => {
            if (err.message === "SESSION_EXPIRED") {
                showToast("Sesi habis, silakan login ulang", "danger");
                setTimeout(() => {
                    window.location.href = "{{ route('admin.login') }}";
                }, 2000);
            } else {
                console.error(err);
                showToast(errorMessage + " (" + err.message + ")", "danger");
            }
        });
    });
}


// 🔘 Bulk restore
$("#restore-selected").click(function () {
    handleBulkAction(
        `{{ route('admin.laporan.restore') }}`,
        "Yakin ingin mengembalikan laporan terpilih ke Diproses?",
        "Laporan dipindahkan ke Diproses",
        "Gagal memindahkan laporan"
    );
});

// 🔘 Bulk delete
$("#delete-selected").click(function () {
    handleBulkAction(
        `{{ route('admin.laporan.bulkDelete') }}`,
        "Yakin ingin menghapus laporan terpilih secara permanen?",
        "Laporan berhasil dihapus",
        "Gagal menghapus laporan"
    );
});

    </script>
@endpush

