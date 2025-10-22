@extends('public.layouts.layout')

@section('content')
@push('styles')
<style>
    footer {
        display: none !important;
    }
</style>
@endpush
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            /* background-color: #f6a5c0 !important; */
            /* background-image: url('/images/bg.jpg'); */
            background: linear-gradient(rgb(194, 185, 238), rgb(246, 238, 255));
            background-attachment: fixed;
            margin: 0;
        }
        .profile-card {
            background: rgba(189, 181, 235, 0.95);
            border-radius: 10px;
            padding: 20px;
        }
        .sidebar-img {
            width: 55%;          /* atur persentase lebar kotak */
            aspect-ratio: 3 / 4; /* biar kotaknya punya rasio tetap (opsional) */
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .sidebar-img img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* biar nggak ketarik aneh */
        }
    </style>
</head>
<body>
    {{-- @include('public.layouts.navbar') --}}

    <!-- Profile Card -->
    <div class="container my-4">
        <div class="profile-card row p-4">
            <!-- Foto Profil -->
            <div class="col-md-3 d-flex justify-content-center align-items-start">
                <div class="sidebar-img">
                    @if($student && $student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}"
                            alt="Foto Profil"
                            class="img-fluid">
                    @else
                        <img src="{{ asset('images/default-avatar.png') }}"
                            alt="Foto Profil"
                            class="img-fluid">
                    @endif
                </div>
            </div>


            <!-- Data Student -->
            <div class="col-md-9">
                <div class="mb-3">
                    <label class="form-label">NISN</label>
                    <input type="text" class="form-control" value="{{ $student->nisn ?? '' }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" class="form-control" value="{{ $student->student_name ?? '' }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kelas</label>
                    <input type="text" class="form-control" value="{{ $student->class->class_name ?? '' }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="{{ $student->user->email ?? '' }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" value="********" readonly>
                </div>

                {{-- Logout --}}
                <form id="logout-form" method="POST" action="{{ route('public.logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="dt dt-btn create">Log Out</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
@endsection
