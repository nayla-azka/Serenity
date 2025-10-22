@extends('public.layouts.layout')

@section('title', 'Arsip Percakapan')

@push('styles')
<style>
    .archive-card {
        transition: all 0.3s ease;
        border: 1px solid #e3e6f0;
        border-radius: 10px;
        overflow: hidden;
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
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-start">
            <!-- Tombol Back -->
            <a href="{{ route('public.konseling.index') }}" class="btn-back me-3">
                <span class="btn-back__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" height="20" width="20">
                        <path d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z" fill="#fff"/>
                        <path d="m237.248 512 265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z" fill="#fff"/>
                    </svg>
                </span>
            </a>

            <!-- Judul + Deskripsi -->
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-archive text-serenity me-2"></i>
                    Arsip Percakapan
                </h4>
                <p class="text-muted mb-0">Riwayat percakapan konseling yang telah diarsipkan</p>
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
                        <h3 class="mb-0">{{ $archivedSessions->unique('id_counselor')->count() }}</h3>
                        <small class="opacity-75">Konselor</small>
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
                            <img src="{{ asset('storage/' . $archive->counselor_photo) }}"
                                 class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;"
                                 onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22><rect width=%22100%25%22 height=%22100%25%22 fill=%22%23ddd%22/><text x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22>👤</text></svg>'">
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $archive->counselor_name ?? 'Unknown Counselor' }}</h6>
                                <small class="opacity-75">{{ $archive->topic ?? 'General Consultation' }}</small>
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
                                <span class="badge bg-secondary">Archived</span>
                            </div>
                        </div>
                    </div>

                    <div class="archive-preview">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            Diarsip: {{ \Carbon\Carbon::parse($archive->archived_at)->format('d M Y, H:i') }}
                        </small>
                        @if($archive->session_started_at)
                        <p class="mt-2 mb-0 small">
                            <i class="fas fa-clock me-1"></i>
                            Sesi: {{ \Carbon\Carbon::parse($archive->session_started_at)->format('d M Y') }}
                            @if($archive->session_ended_at)
                                - {{ \Carbon\Carbon::parse($archive->session_ended_at)->format('d M Y') }}
                            @endif
                        </p>
                        @endif
                        @if($archive->last_message_at)
                        <p class="mt-1 mb-0 small">
                            <i class="fas fa-comment me-1"></i>
                            Pesan terakhir: {{ \Carbon\Carbon::parse($archive->last_message_at)->format('d M Y, H:i') }}
                        </p>
                        @endif
                    </div>

                    <div class="archive-footer">
                        {{-- FIXED: Use id_session instead of original_session_id --}}
                        <a href="{{ route('public.konseling.archive.show', $archive->id_session) }}"
                           class="dt dt-btn create btn-sm">
                            <i class="fas fa-eye me-1"></i>
                            Lihat Percakapan
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="empty-archive">
                    <i class="fas fa-archive"></i>
                    <h5 class="mt-3">Belum Ada Arsip</h5>
                    <p class="text-muted mb-3">
                        Percakapan akan otomatis diarsipkan ketika Anda memulai sesi baru dengan konselor yang sama
                        dan memilih untuk menyimpan percakapan sebelumnya.
                    </p>
                    <div class="alert alert-info text-start mx-auto" style="max-width: 600px;">
                        <p><i class="bi bi-info-circle icon-small"></i>Bagaimana cara mengarsipkan percakapan?</p>
                        <ol class="mb-0 small">
                            <li>Tunggu konselor mengakhiri sesi konseling</li>
                            <li>Klik "Start New Session" pada sesi yang telah berakhir</li>
                            <li>Pilih "Archive Previous Messages" untuk menyimpan riwayat percakapan</li>
                            <li>Percakapan lama akan tersimpan di arsip dan dapat diakses kapan saja</li>
                        </ol>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection