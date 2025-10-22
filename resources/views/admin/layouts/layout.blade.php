<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="pusher-key" content="{{ env('PUSHER_APP_KEY') }}">
<meta name="pusher-cluster" content="{{ env('PUSHER_APP_CLUSTER') }}">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        {{-- Bootstrap Icons --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link rel="stylesheet" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        {{-- Bootstrap JS --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')


    <title>Serenity</title>
 <style>
    html, body {
        height: 100%;
        margin: 0;
    }

    /* Navbar fixed */
    .admin-navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 60px;
        z-index: 1000;
    }

    /* Sidebar fixed */
    .admin-sidebar {
        position: fixed;
        top: 60px; /* di bawah navbar */
        left: 0;
        bottom: 0;
        width: 220px;
        overflow-y: auto;
        z-index: 999;
    }

    /* Main content wrapper */
    .admin-main {
        margin-top: 60px;   /* tinggi navbar */
        margin-left: 220px; /* lebar sidebar */
        height: calc(100vh - 60px);
        overflow: hidden; /* cegah scroll global */
        padding: 16px;
    }

    /* Card di dalam main */
    .admin-card {
        height: 100%;
        overflow-y: auto; /* biar konten panjang tetap bisa di-scroll */
        background-color: rgba(131, 122, 182, 0.142);
    }

    .wrap-text{
        word-break: break-word;
        overflow-wrap: break-word;
    }

    /* === Global DataTables Styling === */
    table.dataTable {
        border-collapse: collapse;
        width: 100% !important;
        font-size: 14px;
        table-layout: fixed !important;
    }

    /* Grid garis */
    table.dataTable th,
    table.dataTable td {
        border: 1px solid rgb(184, 178, 198) !important;
        padding: 8px 12px;
    }

    /* Header */
    table.dataTable thead th {
        background: rgb(131, 122, 182) !important;
        color: white !important;
        text-align: center;
        vertical-align: middle;
    }

    /* Hover baris */
    table.dataTable tbody tr {
        transition: background-color 0.2s ease;
    }
    table.dataTable tbody tr:hover td {
        background-color: #f3f2fa !important;
    }

    /* Foto biar rapi */
    table.dataTable .photo-img {
        width: 170px;
        height: 100px;
        object-fit: cover;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.078);
        transition: all 0.3s ease;
        cursor: pointer;
    }
    table.dataTable .photo-img:hover {
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.073);
        transform: translateY(-2px) scale(1.03);
    }

    /* Deskripsi biar wrap */
    table.dataTable .wrap-text {
        white-space: normal !important;
        word-wrap: break-word;
        max-width: 400px;
    }

    /* Tombol aksi di tabel */
    table.dataTable td .btn {
        margin: 2px;
    }

    /* Sticky header saat scroll (DataTables ScrollX/ScrollY) */
    .dataTables_scrollHead table thead th {
        background-color: rgb(131, 122, 182) !important;
        color: #fff !important;
        text-align: center;
        vertical-align: middle;
        font-size: 14px;
    }

    .dataTables_scrollHeadInner,
    .dataTables_scrollHeadInner table,
    .dataTables_scrollBody table {
        width: 100% !important;
        table-layout: fixed;
    }

    .dataTables_scrollHeadInner th,
    .dataTables_scrollBody td {
        box-sizing: border-box;
    }

    /* === Tombol khusus === */
    .btn-serenity{
        color: rgb(248, 246, 255) !important;
        background-color: rgb(131, 122, 182) !important;
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

    /* Variasi warna tombol */
    .dt-btn.edit {
      color: white;
      border-color: #0d6efd33;
      background-color: #0d6efd;
    }

    .dt-btn.delete {
      color: white;
      border-color: #dc354533;
      background-color: #dc3545;
    }

    .dt-btn.terima {
      color: white;
      background-color: #1a8f59;
    }

    .dt-btn.pending {
      color: white;
      background-color: #ffca2c;
    }

    .dt-btn.edit:hover {
      color: #0d6efd;
      border-color: #0d6efd33;
      background-color: #f8fbff;
      transform: translateY(-2px);
      box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;
    }

    .dt-btn.delete:hover {
      color: #dc3545;
      border-color: #dc354533;
      background-color: #fff5f6;
      transform: translateY(-2px);
      box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;
    }

    .dt-btn.terima:hover {
      color: #1a8f59;
      background-color: #f8fbff;
      transform: translateY(-2px);
      box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;
    }

    .dt-btn.pending:hover {
      color: #ffca2c;
      background-color: #f8fbff;
      transform: translateY(-2px);
      box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;
    }

    .dt-btn.create {
      color: #faedff;
      background-color: #837ab6;
    }
    .dt-btn.create:hover {
      background-color: #6b61a4;
    }

    /* Card style */
    .card-style{
        border: 1px solid rgba(0, 0, 0, 0.1);
        cursor: pointer;
        font-family: system-ui, -apple-system, "Helvetica Neue", Helvetica, Arial, sans-serif;
        transition: all 250ms;
        user-select: none;
    }
    .card-style:hover,
    .card-style:focus {
      border-color: rgba(0, 0, 0, 0.15);
      box-shadow: rgba(130, 111, 160, 0.189) 0 4px 12px;
      transform: translateY(-3px);
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

    /* Section header */
    .section-header {
        background: linear-gradient(135deg, rgba(131, 122, 182, 0.05), rgba(151, 140, 200, 0.02));
        border-left: 4px solid #837ab6;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-header-back .btn-back__icon svg {
      fill: black;
    }

    /* Search & show entries */
    .dataTables_filter input {
        border: 1px solid rgb(184, 178, 198);
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 14px;
        color: #333;
        background-color: #fff;
        transition: all 0.3s ease;
    }
    .dataTables_filter input:focus {
        border-color: rgb(131, 122, 182);
        box-shadow: 0 0 6px rgba(131, 122, 182, 0.4);
        outline: none;
    }
    .dataTables_length select {
        border: 1px solid rgb(184, 178, 198);
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 14px;
        color: #333;
        background-color: #fff;
        transition: all 0.3s ease;
    }
    .dataTables_length select:focus {
        border-color: rgb(131, 122, 182);
        box-shadow: 0 0 6px rgba(131, 122, 182, 0.4);
        outline: none;
    }
    .dataTables_length label,
    .dataTables_filter label {
        font-weight: 500;
        color: #333;
    }

    /* Alert fixed */
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
        border-radius: 0.5rem;
        animation: slideDown 0.4s ease;
    }

    /* Toolbar sticky */
    #toolbar {
        position: sticky;
        top: 0;
        background: rgb(222, 220, 240);
        z-index: 20;
        padding: 6px 8px;
    }

    .text-serenity{
        color: rgb(131, 122, 182);
    }
</style>

    </head>

    <body class="d-flex flex-column min-vh-100">
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
        <div id="alert-container" class="alert-fixed"></div>

        {{-- Main wrapper (fills space between navbar and footer) --}}
        <div class="flex-fill" data-bs-spy="scroll" data-bs-target="#main_menu_area" data-bs-root-margin="0px 0px -40%"
        data-bs-smooth-scroll="true" class="scrollspy-example " tabindex="0">
            @include('admin.layouts.header')

            <!-- Page Content -->
            <main class="flex" style="padding-top: 60px;">
                @include('admin.layouts.sidebar')
                <div class="card flex-1 m-4 p-4 text-gray-800 dark:bg-gray-900 dark:text-white rounded" style="background-color: rgba(131, 122, 182, 0.142); overflow-y: auto;">
                    @yield('content')
                </div>
            </main>
        </div>

       {{-- AlpineJS --}}
<script src="//unpkg.com/alpinejs" defer></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;

    fetch("/set-timezone", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ timezone: tz })
    });
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
function confirmDelete(button) {
    let form = $(button).closest("form");

    showConfirm("Apakah Anda yakin ingin menghapus artikel ini?", function () {
        form.submit(); // Only submit if user clicks YES
    }, "Konfirmasi Hapus");
}

</script>

@stack('scripts')

    </body>
</html>
