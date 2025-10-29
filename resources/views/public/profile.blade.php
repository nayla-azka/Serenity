@extends('public.layouts.layout')

@push('styles')
<style>
    footer {
        display: none !important;
    }

    body {
        background: linear-gradient(135deg, rgb(194, 185, 238), rgb(246, 238, 255));
        background-attachment: fixed;
        min-height: 100vh;
    }

    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .profile-header {
        background: linear-gradient(135deg, rgb(189, 181, 235) 0%, rgb(210, 203, 245) 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 24px rgba(189, 181, 235, 0.3);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .profile-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(10deg); }
    }

    .profile-header h2 {
        font-weight: 700;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
        font-size: 1.75rem;
    }

    .profile-header p {
        opacity: 0.95;
        position: relative;
        z-index: 1;
        margin-bottom: 0;
    }

    .profile-main-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .profile-sidebar {
        background: linear-gradient(135deg, rgba(189, 181, 235, 0.08), rgba(210, 203, 245, 0.03));
        padding: 2.5rem 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        border-right: 1px solid rgba(189, 181, 235, 0.15);
    }

    .profile-photo-container {
        position: relative;
        margin-bottom: 1.5rem;
        width: 200px;
        height: 200px;
    }

    .profile-photo {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid white;
        box-shadow: 0 8px 24px rgba(189, 181, 235, 0.4);
        transition: transform 0.3s ease;
    }

    .profile-photo:hover {
        transform: scale(1.05);
    }

    .profile-info-badge {
        background: rgba(189, 181, 235, 0.15);
        padding: 0.6rem 1.2rem;
        border-radius: 20px;
        font-size: 0.9rem;
        color: rgb(120, 110, 180);
        font-weight: 600;
        margin-bottom: 0.6rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .profile-info-badge:hover {
        background: rgba(189, 181, 235, 0.25);
        transform: translateX(5px);
    }

    .profile-info-badge i {
        font-size: 1rem;
    }

    .profile-content {
        padding: 2.5rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-section-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: rgb(120, 110, 180);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(189, 181, 235, 0.2);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-section-title i {
        font-size: 1.3rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #5a4d8f;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.95rem;
    }

    .form-control {
        border: 2px solid rgba(189, 181, 235, 0.3);
        border-radius: 10px;
        padding: 0.875rem 1rem;
        background-color: #f8f7fc;
        font-size: 0.95rem;
        color: #4a4a4a;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: rgb(189, 181, 235);
        box-shadow: 0 0 0 0.2rem rgba(189, 181, 235, 0.2);
        outline: none;
        background-color: white;
    }

    .form-control[readonly] {
        background-color: #f8f7fc;
        cursor: not-allowed;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid rgba(189, 181, 235, 0.15);
    }

    .btn-logout {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        border: none;
        padding: 0.875rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .btn-logout:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        background: linear-gradient(135deg, #c82333, #bd2130);
    }

    .student-name-display {
        font-size: 1.3rem;
        font-weight: 700;
        color: rgb(120, 110, 180);
        margin-bottom: 0.5rem;
        text-align: center;
    }

    .student-class-display {
        font-size: 1rem;
        color: #6c757d;
        text-align: center;
        margin-bottom: 1rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .profile-container {
            padding: 1rem;
        }

        .profile-header {
            padding: 1.5rem;
        }

        .profile-header h2 {
            font-size: 1.4rem;
        }

        .profile-sidebar {
            border-right: none;
            border-bottom: 1px solid rgba(189, 181, 235, 0.15);
            padding: 2rem 1.5rem;
        }

        .profile-photo-container {
            width: 150px;
            height: 150px;
        }

        .profile-content {
            padding: 1.5rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-logout {
            width: 100%;
            justify-content: center;
        }

        .form-section-title {
            font-size: 1.1rem;
        }

        .student-name-display {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 576px) {
        .profile-header {
            padding: 1.25rem;
        }

        .profile-header h2 {
            font-size: 1.25rem;
        }

        .profile-header p {
            font-size: 0.9rem;
        }

        .profile-photo-container {
            width: 130px;
            height: 130px;
        }

        .profile-content {
            padding: 1.25rem;
        }

        .form-control {
            padding: 0.75rem;
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@section('content')
<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <h2>
            <i class="fas fa-user-circle me-2"></i>
            Profil Siswa
        </h2>
        <p>Informasi data pribadi dan akun Anda</p>
    </div>

    <!-- Main Profile Card -->
    <div class="profile-main-card">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="profile-sidebar">
                    <!-- Photo -->
                    <div class="profile-photo-container">
                        @if($student && $student->photo)
                            <img src="{{ asset('storage/' . $student->photo) }}"
                                 class="profile-photo" 
                                 alt="Foto Profil">
                        @else
                            <img src="{{ asset('images/default-avatar.png') }}"
                                 class="profile-photo" 
                                 alt="Foto Profil">
                        @endif
                    </div>

                    <!-- Student Info -->
                    <div class="text-center w-100">
                        <div class="student-name-display">
                            {{ $student->student_name ?? 'Nama Siswa' }}
                        </div>
                        <div class="student-class-display">
                            <i class="fas fa-school me-1"></i>
                            {{ $student->class->class_name ?? 'Kelas tidak tersedia' }}
                        </div>
                        
                        <div class="profile-info-badge">
                            <i class="fas fa-id-card"></i>
                            NIS: {{ $student->nis ?? '-' }}
                        </div>
                        <div class="profile-info-badge">
                            <i class="fas fa-user-graduate"></i>
                            Siswa
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="col-md-8">
                <div class="profile-content">
                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-user"></i>
                            Informasi Pribadi
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">NIS</label>
                                    <input type="text" class="form-control" 
                                           value="{{ $student->nis ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" 
                                           value="{{ $student->student_name ?? '' }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kelas</label>
                            <input type="text" class="form-control" 
                                   value="{{ $student->class->class_name ?? '' }}" readonly>
                        </div>
                    </div>

                    <!-- Account Security Section -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-lock"></i>
                            Informasi Akun
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" 
                                   value="{{ $student->user->email ?? '' }}" readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" 
                                   value="********" readonly>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Hubungi administrator untuk mengubah password
                            </small>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <form id="logout-form" method="POST" action="{{ route('public.logout') }}" class="w-100">
                            @csrf
                            <button type="submit" class="btn-logout w-100">
                                <i class="fas fa-sign-out-alt"></i>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection