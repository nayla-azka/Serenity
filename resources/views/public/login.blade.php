<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa | Serenity</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div id="alert-container" class="alert-fixed"></div>

<div class="row shadow" style="max-width: 650px; width: 100%; border-radius: 10px; overflow: hidden;">

    {{-- Left Panel: Login Form --}}
    <div class="col-md-6 p-4 curve-divider" style="background-color: #ada4e0;">
        <h4 class="mb-3 text-center">LOGIN</h4>

        <form method="POST" action="{{ route('public.login') }}">
            @csrf

            <div class="mb-2">
                <label style="font-size: 0.85rem;">NIS (Nomor Induk Siswa)</label>
                <input type="text" name="nis"
                       class="form-control form-control-sm @error('nis') is-invalid @enderror"
                       value="{{ old('nis') }}" required autofocus>
                @error('nis')
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

    {{-- Right Panel: Illustration & Text --}}
    <div class="col-md-6 d-flex flex-column justify-content-center align-items-center text-center p-3"
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
    showAlert("{{ session('success') }}", 'success');
@endif

@if (session('error'))
    showAlert("{{ session('error') }}", 'danger');
@endif

@if ($errors->any())
    let errorMessages = `{!! implode('<br>', $errors->all()) !!}`;
    showAlert(errorMessages, 'danger');
@endif

function showAlert(message, type = 'success') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show mt-2" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    document.getElementById('alert-container').innerHTML += alertHtml;

    // Auto hide after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('#alert-container .alert');
        if (alerts.length > 0) {
            alerts[0].remove();
        }
    }, 5000);
}
</script>
</body>
</html>