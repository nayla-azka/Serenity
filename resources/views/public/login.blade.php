<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa | Serenity</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    /* HP (<= 768px) */
    @media (max-width: 768px) {
        .btn-sm {
            margin-left: 40px;
            margin-right: 40px;
            margin-top: 10px;
            font-size: 0.86rem;
        }
    }
</style>
<body class="bg-light d-flex justify-content-center align-items-center" style="min-height: 100vh;">

<div class="row shadow" style="max-width: 650px; width: 100%; border-radius: 10px; overflow: hidden;">

    {{-- Left Panel: Login Form --}}
    <div class="col-md-6 p-4 curve-divider order-2 order-md-1" style="background-color: #ada4e0;">

        {{-- Gambar versi HP (tampil hanya di HP) --}}
        <div class="text-center mb-3 d-block d-md-none">
            <p class="fw-bold mb-2" style="font-size: 1rem;">
                Menjadi siswa berkarakter<br>bersama Serenity.
            </p>
            <img src="{{ asset('images/login-illustration.png') }}"
                alt="Students"
                class="img-fluid"
                style="max-height: 160px;">
        </div>

        <h4 class="mb-3 text-center">LOGIN</h4>

        <form method="POST" action="{{ route('public.login') }}">
            @csrf

            <div class="mb-2">
                <label style="font-size: 0.85rem;">Email</label>
                <input type="email" name="email"
                       class="form-control form-control-sm @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-2">
                <label style="font-size: 0.85rem;">Password</label>
                <input type="password" name="password"
                       class="form-control form-control-sm @error('password') is-invalid @enderror"
                       required>
                @error('password')
                    <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-sm" style="background-color: #cac3f1;">
                    Login
                </button>
            </div>
        </form>
    </div>

    {{-- Right Panel: Illustration & Text (hanya muncul di Desktop) --}}
    <div class="col-md-6 d-flex flex-column justify-content-center align-items-center text-center p-3 order-1 order-md-2 d-none d-md-flex"
         style="background-color: #ada4e0;">
        <p class="fw-bold mb-3" style="font-size: 1rem;">
            Menjadi siswa berkarakter<br>bersama Serenity.
        </p>
        <img src="{{ asset('images/login-illustration.png') }}"
             alt="Students"
             class="img-fluid"
             style="max-height: 180px;">
    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
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
</body>
</html>
