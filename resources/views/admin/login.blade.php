<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Serenity</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>



<body class="bg-light d-flex justify-content-center align-items-center" style="min-height: 100vh;">

<div class="row shadow" style="max-width: 900px; width: 30%; border-radius: 10px; overflow: hidden;">
    
    {{-- Left Panel: Login Form --}}
    <div class="col-md-12 p-5" style="background-color: #ada4e0;">
        <h3 class="mb-4 text-center">LOGIN</h3>
        <div id="alert-container" class="alert-fixed"></div>

     {{-- @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
     @endif --}}

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid">
                <button type="submit" class="btn" style="background-color: #cac3f1;">
                    Login
                </button>
            </div>
        </form>
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
