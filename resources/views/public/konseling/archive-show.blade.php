@extends('public.layouts.layout')

@push('styles')
<style>
    /* ==================== ARTICLE PAGE STYLES ==================== */
    
    html, body {
        min-height: 100vh;
        margin: 0;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
    }

    /* ==================== CARD STYLES ==================== */
    
    .card-artikel {
        background: linear-gradient(135deg, rgba(245, 216, 255, 0.9), rgba(255, 240, 250, 0.9));
        border-radius: 16px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        box-shadow: 0 4px 20px rgba(131, 122, 182, 0.15);
    }

    .card-artikel:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(131, 122, 182, 0.25);
    }

    .card-artikel .card-body {
        padding: 2rem;
    }

    .card-artikel .card-img-top {
        border-radius: 12px;
        transition: transform 0.3s ease;
    }

    .card-artikel .card-img-top:hover {
        transform: scale(1.02);
    }

    /* ==================== LOADING STATES ==================== */
    
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }

    /* ==================== LIKE/INTERACTION STYLES ==================== */
    
    .liked {
        color: #e74c3c !important;
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .article-stats a {
        color: #6c5ce7;
        text-decoration: none;
        padding: 4px 10px;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .article-stats a:hover {
        background: rgba(108, 92, 231, 0.1);
        color: #e74c3c;
        transform: translateY(-2px);
    }

    .like-btn.liked .bi-heart {
        color: #e74c3c !important;
    }

    /* ==================== COMMENT STYLES ==================== */
    
    .comment-item,
    .reply-item {
        transition: all 0.3s ease;
        border-radius: 12px;
        background: white;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .comment-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(131, 122, 182, 0.15);
    }

    .reply-item {
        border-left: 3px solid #837ab6 !important;
        background: rgba(248, 249, 250, 0.7);
        border-radius: 0 12px 12px 0;
        margin-left: 2rem;
    }

    .reply-item:hover {
        border-left-color: #6c5ce7 !important;
        background: rgba(248, 249, 250, 0.9);
    }

    .highlight-comment {
        background: linear-gradient(90deg, #fff3cd 0%, #fffef7 100%) !important;
        border-left: 4px solid #ffc107 !important;
        box-shadow: 0 0 20px rgba(255, 193, 7, 0.4) !important;
        animation: highlightPulse 0.6s ease-in-out;
    }

    @keyframes highlightPulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.02);
        }
    }

    /* ==================== ARTICLE CONTENT ==================== */
    
    .artikel-content {
        line-height: 1.8;
        font-size: 1.05rem;
        color: #2d3748;
    }

    .artikel-content img {
        max-width: 100% !important;
        height: auto !important;
        display: block !important;
        border-radius: 12px;
        margin: 1.5rem auto;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .artikel-content h1,
    .artikel-content h2,
    .artikel-content h3 {
        color: #837ab6;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }

    .artikel-content p {
        margin-bottom: 1.25rem;
    }

    .artikel-content blockquote {
        border-left: 4px solid #837ab6;
        padding-left: 1.5rem;
        font-style: italic;
        color: #666;
        margin: 1.5rem 0;
    }

    /* ==================== SIDEBAR STYLES ==================== */
    
    .kartubaru {
        border-radius: 12px;
        transition: all 0.3s ease-in-out;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        cursor: pointer;
        border: 1px solid rgba(131, 122, 182, 0.1);
        background: white;
    }

    .kartubaru:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(131, 122, 182, 0.3);
    }

    /* ==================== COMMENT REMINDER ==================== */
    
    .comment-reminder {
        background: linear-gradient(135deg, #e6f4ea, #f0fdf4);
        border-left: 4px solid #10b981;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 10px rgba(16, 185, 129, 0.1);
    }

    .comment-reminder ul {
        margin-bottom: 0.5rem;
    }

    .comment-reminder li {
        margin-bottom: 0.5rem;
    }

    /* ==================== FORM STYLES ==================== */
    
    .form-control {
        border-radius: 10px;
        border: 2px solid rgba(131, 122, 182, 0.2);
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #837ab6;
        box-shadow: 0 0 0 0.2rem rgba(131, 122, 182, 0.25);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    /* ==================== BUTTON STYLES ==================== */
    
    .btn-action {
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* ==================== RESPONSIVE STYLES ==================== */

    @media (max-width: 992px) {
        .card-artikel .card-body {
            padding: 1.5rem;
        }

        .artikel-content {
            font-size: 1rem;
        }

        .reply-item {
            margin-left: 1rem;
        }
    }

    @media (max-width: 768px) {
        .card-artikel .card-body {
            padding: 1.25rem;
        }

        .artikel-content {
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .comment-item,
        .reply-item {
            padding: 1rem;
        }

        .reply-item {
            margin-left: 0.5rem;
        }

        .comment-reminder {
            padding: 1rem;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 576px) {
        .card-artikel .card-body {
            padding: 1rem;
        }

        .artikel-content {
            font-size: 0.9rem;
        }

        .article-stats {
            font-size: 0.85rem;
        }

        h1 {
            font-size: 1.5rem !important;
        }

        .btn-action {
            font-size: 0.85rem;
            padding: 5px 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-4 py-md-5">
    <div class="row g-4">
        {{-- Main Article Content --}}
        <div class="col-lg-8">
            {{-- Article Card --}}
            <div class="card card-artikel shadow-lg border-0 mb-4">
                {{-- Article Image --}}
                @if($artikel->photo)
                    <img src="{{ asset('storage/' . $artikel->photo) }}"
                         class="card-img-top p-3 p-md-4"
                         alt="{{ $artikel->title }}"
                         style="max-height: 450px; width: auto; display: block; margin: 0 auto; object-fit: cover;">
                @endif

                <div class="card-body">
                    {{-- Title --}}
                    <h1 class="fw-bold mb-3 text-serenity">{{ $artikel->title }}</h1>

                    {{-- Author & Date --}}
                    <div class="d-flex align-items-center text-muted mb-3">
                        <i class="fas fa-user-circle me-2"></i>
                        <span class="me-3">{{ $artikel->author_name ?? 'Unknown' }}</span>
                        @if($artikel->created_at)
                            <i class="fas fa-calendar-alt me-2"></i>
                            <span>{{ \Carbon\Carbon::parse($artikel->created_at)->format('d M Y') }}</span>
                        @endif
                    </div>

                    <hr class="my-4">

                    {{-- Content --}}
                    <div class="artikel-content">
                        {!! $artikel->content !!}
                    </div>

                    <hr class="my-4">

                    {{-- Statistics --}}
                    <div class="article-stats d-flex flex-wrap align-items-center gap-3">
                        <span class="text-muted">
                            <i class="fas fa-eye me-1"></i>
                            {{ $artikel->total_views }} views
                        </span>

                        {{-- Likes --}}
                        @auth
                            <a href="javascript:void(0)"
                               class="like-btn"
                               data-type="article"
                               data-id="{{ $artikel->article_id }}">
                                <i class="bi bi-heart-fill"></i>
                                <span class="like-count">{{ $artikel->total_likes ?? 0 }}</span> likes
                            </a>
                        @else
                            <span class="text-muted">
                                <i class="bi bi-heart me-1"></i>
                                {{ $artikel->total_likes }} likes
                            </span>
                        @endauth

                        {{-- Comments --}}
                        @auth
                            <a href="#comment-section">
                                <i class="fas fa-comments"></i>
                                <span id="total-comments">{{ $artikel->activeComments()->count() }}</span> komentar
                            </a>
                        @else
                            <span class="text-muted">
                                <i class="fas fa-comments me-1"></i>
                                <span id="total-comments">{{ $artikel->activeComments()->count() }}</span> komentar
                            </span>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar - Related Articles --}}
        <div class="col-lg-4">
            <div class="card card-artikel shadow-sm border-0 position-sticky" style="top: 20px;">
                <div class="card-body">
                    <h5 class="fw-bold mb-4 text-serenity">
                        <i class="fas fa-newspaper me-2"></i>
                        Artikel Lainnya
                    </h5>

                    @forelse($artikelLainnya as $lain)
                        <a href="{{ route('public.artikel_show', $lain->article_id) }}"
                           class="card mb-3 text-decoration-none text-dark shadow-sm border-0 kartubaru">
                            <div class="row g-0">
                                {{-- Thumbnail --}}
                                @if($lain->photo)
                                    <div class="col-4">
                                        <img src="{{ asset('storage/' . $lain->photo) }}"
                                             alt="{{ $lain->title }}"
                                             class="img-fluid rounded-start"
                                             style="height: 100px; width: 100%; object-fit: cover;">
                                    </div>
                                @endif
                                <div class="{{ $lain->photo ? 'col-8' : 'col-12' }}">
                                    <div class="card-body p-3">
                                        <h6 class="card-title mb-2 fw-semibold" style="line-height: 1.4;">
                                            {{ Str::limit($lain->title, 60) }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ \Carbon\Carbon::parse($lain->created_at)->format('d M Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <p class="text-muted text-center py-3">
                            <i class="fas fa-inbox fs-3 d-block mb-2"></i>
                            Belum ada artikel lainnya
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5">

    {{-- Comment Section --}}
    @auth
    <div class="comment-reminder">
        <h6 class="fw-bold mb-3">
            <i class="fas fa-info-circle me-2"></i>
            Perhatikan Sebelum Berkomentar
        </h6>
        <p class="mb-2">Mari bangun forum yang aman dan nyaman dengan menghindari:</p>
        <ul class="mb-2">
            <li>❌ Komentar yang menghina atau menyerang pribadi</li>
            <li>❌ Kata-kata kasar atau diskriminatif</li>
            <li>❌ Gosip atau informasi pribadi orang lain</li>
        </ul>
        <p class="mb-0 fw-semibold">
            <i class="fas fa-heart text-danger me-1"></i>
            Setiap kata kita bisa berdampak – pastikan dampaknya positif!
        </p>
    </div>

    {{-- Comment Form & List --}}
    <div class="mt-4" id="comment-section">
        {{-- Comment Form --}}
        <div class="card card-artikel shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-4">
                    <i class="fas fa-pen me-2"></i>
                    Tulis Komentar
                </h5>
                <form id="comment-form">
                    @csrf
                    <div class="d-flex gap-3">
                        <img src="{{ Auth::user()->siswa ? (Auth::user()->siswa->photo ? asset('storage/' . Auth::user()->siswa->photo) : '/images/default-avatar.png') : (Auth::user()->counselorProfile ? (Auth::user()->counselorProfile->photo ? asset('storage/' . Auth::user()->counselorProfile->photo) : '/images/default-avatar.png') : '/images/default-avatar.png') }}"
                             alt="{{ Auth::user()->name }}"
                             class="rounded-circle"
                             width="50" height="50"
                             style="object-fit: cover;">
                        <div class="flex-grow-1">
                            <textarea id="comment-text" name="comment_text" class="form-control"
                                      rows="3" placeholder="Bagikan pemikiran Anda..." required></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Gunakan bahasa yang sopan dan membangun
                                </small>
                                <button type="submit" class="dt-btn create">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    <span class="submit-text">Kirim</span>
                                    <div class="spinner-border spinner-border-sm d-none ms-2"></div>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Comments List --}}
        <div class="card card-artikel shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold mb-4">
                    <i class="fas fa-comments me-2"></i>
                    Komentar (<span id="total-comments">{{ $artikel->activeComments()->count() ?? 0 }}</span>)
                </h5>

                <div id="comments-container">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Memuat komentar...</span>
                        </div>
                        <div class="mt-3 text-muted">Memuat komentar...</div>
                    </div>
                </div>

                {{-- Load More Comments --}}
                <div id="load-more-container" class="text-center mt-4 d-none">
                    <button id="load-more-comments" class="dt-btn" data-page="2">
                        <span class="load-text">Muat Lebih Banyak</span>
                        <div class="spinner-border spinner-border-sm d-none ms-2"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Report Modal --}}
    <div class="modal fade" id="reportModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-flag text-danger me-2"></i>
                        Laporkan Konten
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="report-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="report-target-type" name="target_type">
                        <input type="hidden" id="report-target-id" name="target_id">

                        <div class="mb-3">
                            <label for="report-reason" class="form-label fw-semibold">Alasan Laporan</label>
                            <select class="form-select" id="report-reason" name="reason" required>
                                <option value="">Pilih alasan...</option>
                                <option value="Spam">Spam</option>
                                <option value="Konten tidak pantas">Konten tidak pantas</option>
                                <option value="Bahasa kasar">Bahasa kasar</option>
                                <option value="Ujaran kebencian">Ujaran kebencian</option>
                                <option value="Informasi palsu">Informasi palsu</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="custom-reason">
                            <label for="custom-reason-text" class="form-label fw-semibold">Jelaskan Alasan Anda</label>
                            <textarea class="form-control" id="custom-reason-text" rows="3"
                                      placeholder="Tuliskan alasan laporan Anda..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-danger rounded-pill">
                            <i class="fas fa-flag me-1"></i>
                            <span class="submit-text">Kirim Laporan</span>
                            <div class="spinner-border spinner-border-sm d-none ms-2"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @else
    <div class="card card-artikel shadow-sm border-0 text-center py-5">
        <div class="card-body">
            <i class="fas fa-lock fs-1 text-muted mb-3"></i>
            <h5 class="fw-bold mb-3">Silakan Login untuk Berkomentar</h5>
            <p class="text-muted mb-4">Bergabunglah dengan diskusi kami dan bagikan pendapat Anda</p>
            <a href="{{ route('public.login') }}" class="dt-btn create">
                <i class="fas fa-sign-in-alt me-2"></i>
                Login Sekarang
            </a>
        </div>
    </div>
    @endauth
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-scroll to bottom
    const chatBox = document.getElementById('chat-box');
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
});
</script>
@endpush