<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="pusher-key" content="{{ env('PUSHER_APP_KEY') }}">
    <meta name="pusher-cluster" content="{{ env('PUSHER_APP_CLUSTER') }}">

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('images/logoSerenity.jpg') }}" type="image/x-icon">

    {{-- CSS Libraries --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    @stack('styles')

    <title>Serenity - Bimbingan Konseling Digital</title>

    <style>
        /* ==================== GLOBAL STYLES ==================== */
        
        html, body {
            height: 100%;
            margin: 0;
            background: linear-gradient(135deg, rgb(194, 185, 238), rgb(246, 238, 255));
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* ==================== BUTTON STYLES ==================== */
        
        .btn-serenity {
            color: rgb(248, 246, 255) !important;
            background: linear-gradient(135deg, rgb(131, 122, 182), rgb(162, 122, 182)) !important;
            border: none !important;
            padding: 10px 24px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(131, 122, 182, 0.3);
        }

        .btn-serenity:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(131, 122, 182, 0.4);
            background: linear-gradient(135deg, rgb(120, 110, 170), rgb(150, 110, 170)) !important;
        }

        /* ==================== TABLE STYLES ==================== */
        
        #konselor-table {
            width: 100% !important;
            overflow-x: auto !important;
        }

        td.wrap-text {
            white-space: normal !important;
            word-wrap: break-word;
            max-width: 700px;
        }

        .photo-img {
            width: auto;
            height: auto;
            object-fit: cover;
            border-radius: 8px;
        }

        /* ==================== ALERT STYLES ==================== */
        
        .alert-fixed {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: auto;
            max-width: 600px;
            z-index: 3000;
            pointer-events: none;
        }

        .alert-fixed .alert {
            pointer-events: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            border-radius: 12px;
            animation: slideDown 0.4s ease;
            border: none;
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

        /* ==================== CUSTOM BUTTONS ==================== */
        
        .dt-btn {
            align-items: center;
            background-color: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            box-shadow: rgba(0, 0, 0, 0.02) 0 1px 3px 0;
            color: rgba(0, 0, 0, 0.85);
            cursor: pointer;
            display: inline-flex;
            font-family: system-ui, -apple-system, "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 14px;
            font-weight: 600;
            justify-content: center;
            line-height: 1.25;
            min-height: 2.5rem;
            padding: 10px 20px;
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
            background: linear-gradient(135deg, #837ab6, #a27ab6);
            border: none;
        }

        .dt-btn.create:hover {
            background: linear-gradient(135deg, #6b61a4, #8a61a4);
        }

        /* ==================== INPUT STYLES ==================== */
        
        .input-style {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 3px solid transparent;
            border-radius: 10px;
            padding: 10px 14px;
            border: 2px solid rgba(131, 122, 182, 0.2);
        }

        .input-style:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(131, 122, 182, 0.15);
            border-left-color: #837ab6;
            background-color: #fafafa;
        }

        .input-style:focus {
            border-color: #837ab6;
            box-shadow: 0 0 0 0.2rem rgba(131, 122, 182, 0.25);
            outline: none;
        }

        /* ==================== BACK BUTTON ==================== */
        
        .btn-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            width: 110px;
            height: 40px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-back__icon {
            position: absolute;
            left: 3px;
            top: 3px;
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgb(131, 122, 182), rgb(162, 122, 182));
            border-radius: 10px;
            transition: width 0.4s ease;
            z-index: 1;
        }

        .btn-back__text {
            margin-left: 50px;
            position: relative;
            z-index: 2;
            transition: color 0.4s ease;
            font-weight: 600;
        }

        .btn-back:hover .btn-back__icon {
            width: 104px;
        }

        .btn-back:hover .btn-back__text {
            color: #fff;
        }

        /* ==================== CARD STYLES ==================== */
        
        .kartubaru {
            border-radius: 12px;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            cursor: pointer;
            border: 1px solid rgba(131, 122, 182, 0.1);
        }

        .kartubaru:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 0 15px rgba(131, 122, 182, 0.4),
                0 0 25px rgba(131, 122, 182, 0.2);
        }

        /* ==================== UTILITY CLASSES ==================== */
        
        .bg-serenity {
            color: rgb(248, 246, 255) !important;
            background: linear-gradient(135deg, #837ab6, #a27ab6) !important;
        }

        .text-serenity {
            color: rgb(131, 122, 182) !important;
        }

        /* ==================== MODAL STYLES ==================== */
        
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            border-bottom: 1px solid rgba(131, 122, 182, 0.1);
        }

        .modal-footer {
            border-top: 1px solid rgba(131, 122, 182, 0.1);
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
        }

        /* ==================== TOAST STYLES ==================== */
        
        .toast {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* ==================== RESPONSIVE STYLES ==================== */

        @media (max-width: 992px) {
            .btn-back {
                width: 90px;
                height: 36px;
            }

            .btn-back__icon {
                width: 30px;
                height: 30px;
            }

            .btn-back__text {
                margin-left: 45px;
                font-size: 14px;
            }

            td.wrap-text {
                max-width: 400px;
            }

            .dt-btn {
                font-size: 13px;
                padding: 8px 16px;
            }
        }

        @media (max-width: 768px) {
            .btn-back {
                width: 70px;
                height: 34px;
            }

            .btn-back__text {
                display: none;
            }

            .btn-back__icon {
                width: 28px;
                height: 28px;
            }

            .kartubaru {
                margin-bottom: 15px;
            }

            td.wrap-text {
                max-width: 250px;
                font-size: 14px;
            }

            .alert-fixed {
                width: 95%;
                max-width: none;
            }
        }

        @media (max-width: 576px) {
            body {
                font-size: 14px;
            }

            .dt-btn {
                font-size: 12px;
                padding: 6px 12px;
                min-height: 2rem;
            }

            .btn-serenity {
                padding: 8px 18px;
                font-size: 14px;
            }

            .input-style {
                padding: 8px 12px;
            }
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    {{-- Alert Container --}}
    <div id="alert-container" class="alert-fixed"></div>

    {{-- Main Content Wrapper --}}
    <main class="flex-grow-1" data-bs-spy="scroll" data-bs-target="#main_menu_area"
          data-bs-root-margin="0px 0px -40%" data-bs-smooth-scroll="true" tabindex="0">

        {{-- Navbar --}}
        @include('public.layouts.navbar')

        {{-- Page Content --}}
        @yield('content')

        {{-- Footer (conditionally rendered) --}}
        @if (!request()->routeIs('public.lapor'))
            @include('public.layouts.footer')
        @endif
    </main>

    {{-- Confirmation Modal --}}
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="confirmModalTitle">
                        <i class="fas fa-question-circle text-warning me-2"></i>
                        Konfirmasi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="confirmModalMessage">
                    Apakah Anda yakin ingin melanjutkan?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="button" class="btn btn-danger rounded-pill" id="confirmYesBtn">
                        <i class="fas fa-check me-1"></i>Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript Libraries --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        // ==================== TIMEZONE SETUP ====================
        document.addEventListener("DOMContentLoaded", function() {
            const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            const storedTimezone = "{{ session('timezone', '') }}";

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

        // ==================== FLASH MESSAGES ====================
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

        // ==================== TOAST FUNCTION ====================
        function showToast(message, type = "success") {
            let toast = document.createElement("div");
            toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
            toast.role = "alert";
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            document.body.appendChild(toast);

            let bsToast = new bootstrap.Toast(toast, {
                delay: 5000
            });
            bsToast.show();

            toast.addEventListener("hidden.bs.toast", () => {
                toast.remove();
            });
        }

        // ==================== CONFIRM FUNCTION ====================
        function showConfirm(message, callback, title = "Konfirmasi") {
            $("#confirmModalTitle").html(`<i class="fas fa-question-circle text-warning me-2"></i>${title}`);
            $("#confirmModalMessage").text(message);

            $("#confirmYesBtn").off("click");

            $("#confirmYesBtn").on("click", function () {
                $("#confirmModal").modal("hide");
                if (typeof callback === "function") callback();
            });

            $("#confirmModal").modal("show");
        }

        // ==================== GLOBAL UTILITIES ====================
        
        // Smooth scroll to top
        window.scrollToTop = function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        };

        // Loading state helper
        window.setLoadingState = function(element, isLoading) {
            const $el = $(element);
            if (isLoading) {
                $el.prop('disabled', true);
                $el.data('original-html', $el.html());
                $el.html('<span class="spinner-border spinner-border-sm me-1"></span>Memuat...');
            } else {
                $el.prop('disabled', false);
                $el.html($el.data('original-html'));
            }
        };
    </script>

    @stack('scripts')
</body>
</html> 