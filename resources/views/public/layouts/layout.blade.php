<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="icon" href="{{ asset('images/logoSerenity.jpg') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="pusher-key" content="{{ env('PUSHER_APP_KEY') }}">
<meta name="pusher-cluster" content="{{ env('PUSHER_APP_CLUSTER') }}">

    {{-- @vite('resources/js/app.js')
    @vite('resources/js/notifications.js') --}}


    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}"> <!-- CSS kustom -->

    @stack('styles')

    <title>Serenity</title>
    <style>
        html, body {
            height: 100%;
            /* background-color: #f6a5c0 !important; */
            /* background-image: url('/images/bg.jpg'); */
            background: linear-gradient(rgb(194, 185, 238), rgb(246, 238, 255));
            background-attachment: fixed;
            margin: 0;
        }

        /* Custom button */
        .btn-serenity {
            color: rgb(248, 246, 255) !important;
            background-color: rgb(131, 122, 182) !important;
        }

        /* Table wrapper */
        #konselor-table {
            width: 100% !important;
            overflow-x: auto !important; /* biar kolom ga kepotong */
        }

        /* Wrap long text (for Bio column) */
        td.wrap-text {
            white-space: normal !important;
            word-wrap: break-word;
            max-width: 700px; /* prevent table from stretching */
        }

        /* Foto column images */
        .photo-img {
            width: auto;
            height: auto;
            object-fit: cover;
        }

        .alert-fixed {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: auto;
            max-width: 600px;
            z-index: 3000; /* Higher than Bootstrap modal */
            pointer-events: none; /* Allow clicks through except on alert */
        }

        .alert-fixed .alert {
            pointer-events: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            border-radius: 0.5rem;
            animation: slideDown 0.4s ease;
        }

        .dt-btn {
      align-items: center;
      background-color: #FFFFFF;
      border: 1px solid rgba(0, 0, 0, 0.1);
      border-radius: .25rem;
      box-shadow: rgba(0, 0, 0, 0.02) 0 1px 3px 0;
      color: rgba(0, 0, 0, 0.85);
      cursor: pointer;
      display: inline-flex;
      font-family: system-ui, -apple-system, "Helvetica Neue", Helvetica, Arial, sans-serif;
      font-size: 14px;
      font-weight: 600;
      justify-content: center;
      line-height: 1.25;
      min-height: 2rem;
      padding: .5rem 1rem;
      text-decoration: none;
      transition: all 250ms;
      user-select: none;
      vertical-align: middle;
    }

    .dt-btn:hover,
    .dt-btn:focus {
      border-color: rgba(0, 0, 0, 0.15);
      box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;
      color: rgba(0, 0, 0, 0.65);
      transform: translateY(-3px);
    }

    .dt-btn:active {
      background-color: #F0F0F1;
      border-color: rgba(0, 0, 0, 0.15);
      box-shadow: rgba(0, 0, 0, 0.06) 0 2px 4px;
      transform: translateY(0);
    }

        .dt-btn.create {
      color: #faedff;
      background-color: #837ab6;
    }
    .dt-btn.create:hover {
      background-color: #6b61a4;
    }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translate(-50%, -20px);
            }
            to {
                opacity: 1;
                transform: translate(-50%, 0);
            }
        }

            /* Input style */
    .input-style {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border-left: 3px solid transparent;
      border-radius: 6px;
      padding: 6px;
    }
    .input-style:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(131, 122, 182, 0.15);
      border-left-color: #837ab6;
      background-color: #fafafa;
    }

        /* Tombol back */
    .btn-back {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      position: relative;
      width: 100px;
      height: 36px;
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      text-decoration: none;
    }
    .btn-back__icon {
      position: absolute;
      left: 3px;
      top: 3px;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgb(131, 122, 182);
      border-radius: 10px;
      transition: width 0.4s ease;
      z-index: 1;
    }
    .btn-back__text {
      margin-left: 55px;
      position: relative;
      z-index: 2;
      transition: color 0.4s ease;
    }
    .btn-back:hover .btn-back__icon {
      width: 94px;
    }
    .btn-back:hover .btn-back__text {
      color: #fff;
    }

    .kartubaru {
    border-radius: 10px;
    transition: all 0.25s ease-in-out;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    cursor: pointer;
}

.kartubaru:hover {
    transform: scale(1.03);
    box-shadow:
        0 0 10px rgba(131, 122, 182, 0.8),
        0 0 18px rgba(131, 122, 182, 0.5),
        0 0 25px rgba(131, 122, 182, 0.3);
}

.bg-serenity {
    color: rgb(248, 246, 255) !important;
    background-color: #250e2cac !important;
}

    .text-serenity{
        color: rgb(131, 122, 182);
    }

    /* ==================== RESPONSIVE ==================== */

/* Tablet (<= 992px) */
@media (max-width: 992px) {
    .btn-back {
        width: 80px;
        height: 32px;
    }
    .btn-back__icon {
        width: 26px;
        height: 26px;
    }
    .btn-back__text {
        margin-left: 45px;
        font-size: 14px;
    }
    td.wrap-text {
        max-width: 400px;
    }
}

/* HP (<= 768px) */
@media (max-width: 768px) {
    .btn-back {
        width: 60px;
        height: 30px;
    }
    .btn-back__text {
        display: none; /* hide text, cuma icon */
    }
    .kartubaru {
        margin-bottom: 15px; /* kasih jarak antar card */
    }
    td.wrap-text {
        max-width: 250px;
        font-size: 14px;
    }
}

/* HP kecil (<= 576px) */
@media (max-width: 576px) {
    body {
        font-size: 14px;
    }
    .dt-btn {
        font-size: 12px;
        padding: .4rem .75rem;
    }
    .alert-fixed {
        width: 95%;
        max-width: none;
        left: 50%;
        transform: translateX(-50%);
    }
}

    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <div id="alert-container" class="alert-fixed"></div>

    {{-- Main wrapper --}}
    <main class="flex-grow-1" data-bs-spy="scroll" data-bs-target="#main_menu_area"
         data-bs-root-margin="0px 0px -40%" data-bs-smooth-scroll="true" tabindex="0">

        @include('public.layouts.navbar')

        {{-- Page Content --}}
        <!-- Custom Confirmation Modal -->
        <div class="modal fade" id="confirmModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title fw-bold" id="confirmModalTitle">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="confirmModalMessage">
                    Apakah Anda yakin ingin melanjutkan?
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmYesBtn">Ya</button>
                </div>
                </div>
            </div>
        </div>
        @yield('content')


        {{-- Footer --}}
        @if (!request()->routeIs('public.lapor'))
            @include('public.layouts.footer')
        @endif

    </main>

   {{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
{{-- AlpineJS --}}
<script src="//unpkg.com/alpinejs" defer></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    const storedTimezone = "{{ session('timezone', '') }}";

    // Only update if timezone has changed or is not set
    if (!storedTimezone || storedTimezone !== userTimezone) {
        fetch("/set-timezone", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ timezone: userTimezone })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Timezone updated to:', data.timezone);
            }
        })
        .catch(error => {
            console.warn('Failed to set timezone:', error);
        });
    }
});

@if (session('success'))
    showToast("{{ session('success') }}", 'success');
@endif

@if (session('error'))
    showToast("{{ session('error') }}", 'danger');
@endif

@if ($errors->any())
    let errorMessages = `{!! implode('<br>', $errors->all()) !!}`;
    showToast(errorMessages, 'danger');
@endif

// fungsi toast
function showToast(message, type = "success") {
    let toast = document.createElement("div");
    toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
    toast.role = "alert";
    toast.innerHTML = `<div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>`;
    document.body.appendChild(toast);

    let bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    toast.addEventListener("hidden.bs.toast", () => {
        toast.remove();
    });
}

function showConfirm(message, callback, title = "Konfirmasi") {
    $("#confirmModalTitle").text(title);
    $("#confirmModalMessage").text(message);

    // Remove previous click handlers to avoid multiple triggers
    $("#confirmYesBtn").off("click");

    // Add new click handler
    $("#confirmYesBtn").on("click", function () {
        $("#confirmModal").modal("hide");
        if (typeof callback === "function") callback();
    });

    $("#confirmModal").modal("show");
}
</script>

@stack('scripts')

</body>
</html>


</body>
</html>
