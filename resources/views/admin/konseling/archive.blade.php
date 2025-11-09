@extends('admin.layouts.layout')

@section('title', 'Arsip Percakapan - Admin Konseling')

@push('styles')
<style>
    .archive-card {
        transition: all 0.3s ease;
        border: 1px solid #e3e6f0;
        border-radius: 10px;
        overflow: hidden;
    }

    .archive-card:hover {
        box-shadow: 0 4px 15px rgba(131, 122, 182, 0.15);
        transform: translateY(-2px);
        border-color: #837ab6;
    }

    .archive-header {
        background: linear-gradient(135deg, #837ab6 0%, #9f95d3 100%);
        color: white;
        padding: 1rem;
    }

    .archive-info {
        background: #f8f9fa;
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
    }

    .archive-preview {
        padding: 1rem;
        max-height: 100px;
        overflow: hidden;
        position: relative;
    }

    .archive-preview::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        height: 20px;
        width: 100%;
        background: linear-gradient(transparent, white);
    }

    .archive-footer {
        background: #f8f9fa;
        padding: 1rem;
        text-align: center;
        border-top: 1px solid #e3e6f0;
    }

    .stats-card {
        background: linear-gradient(135deg, #837ab6 0%, #9f95d3 100%);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .empty-archive {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
    }

    .empty-archive i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .back-btn {
        background: linear-gradient(135deg, #837ab6, #9f95d3);
        border: none;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 25px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .back-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(131, 122, 182, 0.3);
        color: white;
    }
    .btn-delete-archive {
        background: linear-gradient(135deg, #dc3545, #c82333);
        border: none;
        color: white;
        padding: 0.5rem 1.2rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }

    .btn-delete-archive:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        background: linear-gradient(135deg, #c82333, #bd2130);
    }
    
    .archive-footer {
        background: #f8f9fa;
        padding: 1rem;
        text-align: center;
        border-top: 1px solid #e3e6f0;
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }
</style>
@endpush

@section('content')
    <div class="section-header text-black mb-0" style="margin-bottom: -9px !important;">
        <div class="section-header-back">
          <a href="{{ route('admin.konseling.index') }}" class="btn-back">
            <span class="btn-back__icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" height="20" width="20">
                <path d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z" fill="#fff"/>
                <path d="m237.248 512 265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z" fill="#fff"/>
              </svg>
            </span>
          </a>
        </div>
        <h2 class="section-title"> <i class="fas fa-archive text-serenity me-2"></i> Arsip Percakapan</h2>
    </div>
    
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mt-0" style="margin-bottom: 50px;">Riwayat percakapan konseling yang telah diarsipkan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        @if($archivedSessions->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="stats-card">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h3 class="mb-0">{{ $archivedSessions->count() }}</h3>
                            <small class="opacity-75">Total Arsip</small>
                        </div>
                        <div class="col-md-3">
                            <h3 class="mb-0">{{ $archivedSessions->sum('total_messages') ?: '0' }}</h3>
                            <small class="opacity-75">Total Pesan</small>
                        </div>
                        <div class="col-md-3">
                            <h3 class="mb-0">{{ $archivedSessions->unique('id_student')->count() }}</h3>
                            <small class="opacity-75">Siswa Berbeda</small>
                        </div>
                        <div class="col-md-3">
                            <h3 class="mb-0">{{ $archivedSessions->where('archived_at', '>=', now()->subDays(30))->count() }}</h3>
                            <small class="opacity-75">Bulan Ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Archive List -->
        <div class="row">
            @if($archivedSessions->count() > 0)
                @foreach($archivedSessions as $archive)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="archive-card h-100">
                        <div class="archive-header">
                            <div class="d-flex align-items-center">
                                <img src="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22><rect width=%22100%25%22 height=%22100%25%22 fill=%22%23ddd%22/><text x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22>👤</text></svg>"
                                     class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $archive->student_name ?? $archive->user_name ?? 'Unknown Student' }}</h6>
                                    <small class="opacity-75">{{ $archive->topic ?? 'No Topic' }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="archive-info">
                            <div class="row text-center">
                                <div class="col-6">
                                    <small class="text-muted d-block">Pesan</small>
                                    <strong>{{ $archive->total_messages ?? 0 }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Status</small>
                                    <strong class="badge bg-secondary">Archived</strong>
                                </div>
                            </div>
                        </div>

                        <div class="archive-preview">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                Archived: {{ $archive->archived_at->format('d M Y, H:i') }}
                            </small>
                            @if($archive->session_started_at)
                            <p class="mt-2 mb-0 small">
                                Session: {{ $archive->session_started_at->format('d M Y') }}
                                @if($archive->session_ended_at)
                                    - {{ $archive->session_ended_at->format('d M Y') }}
                                @endif
                            </p>
                            @endif
                        </div>

                        <div class="archive-footer">
                            <a href="{{ route('admin.konseling.archive.show', $archive->id_session) }}"
                            class="dt dt-btn create">
                                <i class="fas fa-eye me-1"></i>
                                Lihat Percakapan
                            </a>
                            <button onclick="deleteArchive({{ $archive->id_session }})"
                                    class="btn-delete-archive">
                                <i class="fas fa-trash-alt"></i>
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-12 mt-4">
                    <div class="empty-archive">
                        <i class="fas fa-archive"></i>
                        <h5 class="mt-3">Belum Ada Arsip</h5>
                        <p class="text-muted">Percakapan yang diarsipkan akan muncul di sini</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

@push('scripts')
<script>
function deleteArchive(sessionId) {
    showConfirm('Apakah Anda yakin ingin menghapus arsip ini secara permanen? Tindakan ini tidak dapat dibatalkan.', function() {
        fetch(`/admin/konseling/archive/${sessionId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the card with animation
                const card = document.getElementById(`archive-card-${sessionId}`);
                if (card) {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';
                    
                    setTimeout(() => {
                        card.remove();
                        
                        // Check if no more archives
                        const remainingCards = document.querySelectorAll('[id^="archive-card-"]');
                        if (remainingCards.length === 0) {
                            location.reload(); // Reload to show empty state
                        }
                    }, 300);
                }

                // Show success toast
                showToast(data.message, 'success');
            } else {
                showToast(data.error || 'Gagal menghapus arsip', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat menghapus arsip', 'danger');
        });
    }, 'Konfirmasi Hapus Arsip');
}
</script>
@endpush
@endsection