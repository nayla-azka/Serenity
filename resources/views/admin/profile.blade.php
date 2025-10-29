@extends('admin.layouts.layout')

@push('styles')
<style>
    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .profile-header {
        background: linear-gradient(135deg, rgb(131, 122, 182) 0%, rgb(151, 140, 200) 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 24px rgba(131, 122, 182, 0.2);
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
        background: rgba(255, 255, 255, 0.1);
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
    }

    .profile-header p {
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }

    .profile-main-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .profile-sidebar {
        background: linear-gradient(135deg, rgba(131, 122, 182, 0.05), rgba(151, 140, 200, 0.02));
        padding: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        border-right: 1px solid rgba(131, 122, 182, 0.1);
    }

    .profile-photo-container {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .profile-photo {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid white;
        box-shadow: 0 8px 24px rgba(131, 122, 182, 0.3);
        transition: transform 0.3s ease;
    }

    .profile-photo:hover {
        transform: scale(1.05);
    }

    .photo-upload-label {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: linear-gradient(135deg, rgb(131, 122, 182), rgb(151, 140, 200));
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(131, 122, 182, 0.4);
        transition: all 0.3s ease;
    }

    .photo-upload-label:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(131, 122, 182, 0.6);
    }

    .photo-upload-label i {
        color: white;
        font-size: 1rem;
    }

    #image-upload {
        display: none;
    }

    .profile-info-badge {
        background: rgba(131, 122, 182, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        color: rgb(131, 122, 182);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .profile-content {
        padding: 2.5rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: rgb(131, 122, 182);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(131, 122, 182, 0.2);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-section-title i {
        font-size: 1.2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #4b4376;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.9rem;
    }

    .form-control {
        border: 2px solid rgba(131, 122, 182, 0.2);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .form-control:focus {
        border-color: rgb(131, 122, 182);
        box-shadow: 0 0 0 0.2rem rgba(131, 122, 182, 0.15);
        outline: none;
    }

    .form-select {
        border: 2px solid rgba(131, 122, 182, 0.2);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        border-color: rgb(131, 122, 182);
        box-shadow: 0 0 0 0.2rem rgba(131, 122, 182, 0.15);
        outline: none;
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid rgba(131, 122, 182, 0.1);
    }

    .btn-save {
        background: linear-gradient(135deg, rgb(131, 122, 182), rgb(151, 140, 200));
        color: white;
        border: none;
        padding: 0.875rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(131, 122, 182, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(131, 122, 182, 0.4);
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
    }

    .btn-logout:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        background: linear-gradient(135deg, #c82333, #bd2130);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(131, 122, 182, 0.15);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        font-size: 1.5rem;
    }

    .stat-icon.primary {
        background: linear-gradient(135deg, rgba(131, 122, 182, 0.2), rgba(151, 140, 200, 0.1));
        color: rgb(131, 122, 182);
    }

    .stat-icon.success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.1));
        color: #10b981;
    }

    .stat-icon.info {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(59, 130, 246, 0.1));
        color: #3b82f6;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2d3748;
    }

    @media (max-width: 768px) {
        .profile-sidebar {
            border-right: none;
            border-bottom: 1px solid rgba(131, 122, 182, 0.1);
        }

        .action-buttons {
            flex-direction: column;
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
            Profil Saya
        </h2>
        <p>Kelola informasi profil dan pengaturan akun Anda</p>
    </div>

    <!-- Main Profile Card -->
    <div class="profile-main-card">
        <form method="POST" action="{{ route('admin.profile.update', $user->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-0">
                <!-- Sidebar -->
                <div class="col-md-4">
                    <div class="profile-sidebar">
                        @if($user->role === 'konselor' && $konselor)
                            <!-- Photo -->
                            <div class="profile-photo-container">
                                <img id="preview-image" 
                                     src="{{ $konselor->photo ? asset('storage/' . $konselor->photo) : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22180%22 height=%22180%22%3E%3Crect width=%22100%25%22 height=%22100%25%22 fill=%22%23ddd%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 font-size=%2260%22%3E👤%3C/text%3E%3C/svg%3E' }}"
                                     class="profile-photo" 
                                     alt="Profile Photo">
                                <label for="image-upload" class="photo-upload-label">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" name="photo" id="image-upload" accept="image/*">
                            </div>

                            <!-- Info Badges -->
                            <div class="text-center">
                                <div class="profile-info-badge">
                                    <i class="fas fa-id-card me-1"></i>
                                    {{ $konselor->nip ?? 'NIP Not Set' }}
                                </div>
                                <div class="profile-info-badge">
                                    <i class="fas fa-graduation-cap me-1"></i>
                                    Kelas {{ $konselor->kelas ?? 'Not Assigned' }}
                                </div>
                                <div class="profile-info-badge">
                                    <i class="fas fa-user-shield me-1"></i>
                                    {{ ucfirst($user->role) }}
                                </div>
                            </div>
                        @else
                            <!-- Admin/Other roles -->
                            <div class="profile-photo-container">
                                <img src="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22180%22 height=%22180%22%3E%3Crect width=%22100%25%22 height=%22100%25%22 fill=%22%23837ab6%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 font-size=%2260%22 fill=%22white%22%3E{{ strtoupper(substr($user->name, 0, 1)) }}%3C/text%3E%3C/svg%3E"
                                     class="profile-photo" 
                                     alt="Profile">
                            </div>

                            <div class="text-center">
                                <h5 class="mb-2" style="color: rgb(131, 122, 182);">{{ $user->name }}</h5>
                                <div class="profile-info-badge">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    {{ ucfirst($user->role) }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Content -->
                <div class="col-md-8">
                    <div class="profile-content">
                        @if($user->role === 'konselor' && $konselor)
                            <!-- Konselor Form -->
                            <div class="form-section">
                                <div class="form-section-title">
                                    <i class="fas fa-user"></i>
                                    Informasi Pribadi
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">NIP</label>
                                            <input type="text" name="nip" class="form-control" 
                                                   value="{{ old('nip', $konselor->nip) }}"
                                                   placeholder="Masukkan NIP">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Nama Konselor</label>
                                            <input type="text" name="counselor_name" class="form-control" 
                                                   value="{{ old('counselor_name', $konselor->counselor_name) }}"
                                                   placeholder="Masukkan nama lengkap">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Kelas</label>
                                            <select name="kelas" class="form-select">
                                                <option value="">-- Pilih Kelas --</option>
                                                <option value="X" @selected($konselor->kelas=='X')>X</option>
                                                <option value="XI" @selected($konselor->kelas=='XI')>XI</option>
                                                <option value="XII & XIII" @selected($konselor->kelas=='XII & XIII')>XII & XIII</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Kontak</label>
                                            <input type="text" name="contact" class="form-control" 
                                                   value="{{ old('contact', $konselor->contact) }}"
                                                   placeholder="Nomor telepon/WA">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="desc" class="form-control" placeholder="Ceritakan sedikit tentang diri Anda...">{{ old('desc', $konselor->desc) }}</textarea>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">
                                    <i class="fas fa-lock"></i>
                                    Keamanan Akun
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="{{ old('email', $user->email) }}"
                                           placeholder="email@example.com">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Password Baru</label>
                                    <input type="password" name="password" class="form-control" 
                                           placeholder="Kosongkan jika tidak ingin mengubah password">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Minimal 8 karakter untuk keamanan yang lebih baik
                                    </small>
                                </div>
                            </div>

                        @else
                            <!-- Admin Form -->
                            <div class="form-section">
                                <div class="form-section-title">
                                    <i class="fas fa-user"></i>
                                    Informasi Pribadi
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="name" class="form-control" 
                                           value="{{ old('name', $user->name) }}"
                                           placeholder="Masukkan nama lengkap">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="{{ old('email', $user->email) }}"
                                           placeholder="email@example.com">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Password Baru</label>
                                    <input type="password" name="password" class="form-control" 
                                           placeholder="Kosongkan jika tidak ingin mengubah password">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Minimal 8 karakter untuk keamanan yang lebih baik
                                    </small>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save"></i>
                                Simpan Perubahan
                            </button>
                            
                            <button type="button" class="btn-logout" onclick="document.getElementById('logout-form').submit()">
                                <i class="fas fa-sign-out-alt"></i>
                                Log Out
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Logout Form (hidden) -->
<form id="logout-form" method="POST" action="{{ route('admin.logout') }}" style="display: none;">
    @csrf
</form>
@endsection

@push('scripts')
<script>
    // Image preview
    $('#image-upload').on('change', function () {
        const [file] = this.files;
        if (file) {
            $('#preview-image').attr('src', URL.createObjectURL(file));
        }
    });

    // Toast function
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

        let bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener("hidden.bs.toast", () => {
            toast.remove();
        });
    }

    // Show session messages
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success'))
            showToast("{{ session('success') }}", "success");
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", "danger");
        @endif
    });
</script>
@endpush
</document_content>