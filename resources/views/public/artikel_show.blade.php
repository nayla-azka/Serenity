@extends('public.layouts.layout')

@section('content')

<style>
html, body {
    min-height: 100vh;
    margin: 0;
    /*background: linear-gradient(rgba(167, 155, 233), rgba(245, 186, 222)); */
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover;
}

    /* Debug styles */
.debug-border {
    border: 1px solid red !important;
}

.debug-bg {
    background-color: rgba(255, 0, 0, 0.1) !important;
}

    .btn-serenity {
        color: rgb(248, 246, 255) !important;
        background-color: rgb(131, 122, 182) !important;
    }
/* ====== CARD STYLE ====== */
    .card-artikel {
        background-color: rgba(245, 216, 255, 0.852);
        border-radius: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    /* Loading styles */
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }

    .liked {
        color: #e74c3c !important;
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    /* Styling khusus untuk statistik artikel */
    .article-stats a {
        color: #6c5ce7;
        text-decoration: none;
        padding: 2px 6px;
        border-radius: 6px;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .article-stats a:hover {
        background: rgba(108, 92, 231, 0.1);
        color: #e74c3c;
    }
    .like-btn.liked {
    color: #e74c3c !important;
}

.like-btn.liked .bi-heart {
    color: #e74c3c !important;
}

.loading {
    opacity: 0.6;
    pointer-events: none;
}

.comment-item {
    transition: all 0.3s ease;
}
/*
.comment-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
} */

.reply-item {
    border-left: 3px solid #e9ecef !important;
    background: rgba(248, 249, 250, 0.5);
    border-radius: 0 8px 8px 0;
    transition: border-color 0.3s ease;
}

.reply-item:hover {
    border-left-color: #6c5ce7 !important;
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
.highlight-comment {
    background: linear-gradient(90deg, #fff3cd 0%, #fffef7 100%) !important;
    border-left: 4px solid #ffc107 !important;
    box-shadow: 0 0 15px rgba(255, 193, 7, 0.3) !important;
    animation: highlightPulse 0.5s ease-in-out;
}

@keyframes highlightPulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.01);
    }
}

    .artikel-content img {
    max-width: 100% !important;
    height: auto !important;
    display: block !important;
}
    .kartubaru {
    border-radius: 10px;
    transition: all 0.25s ease-in-out;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    cursor: pointer;
}

.kartubaru:hover {
    transform: scale(1.03);
    box-shadow:
        0 0 10px rgba(131, 122, 182, 0.8),
        0 0 18px rgba(131, 122, 182, 0.5),
        0 0 25px rgba(131, 122, 182, 0.3);
}

</style>

{{-- <livewire:comments :model="$artikel"/> --}}
    <div class="container py-5">
        <div class="row">
            {{-- KONTEN ARTIKEL --}}
            <div class="col-md-8">
                <div class="card card-artikel shadow-lg border-0 mb-4" style="background: linear-gradient(rgba(167, 155, 233, 0.82), rgba(245, 186, 222, 0.82));">

                    {{-- Foto Artikel (jika ada) --}}
                    @if($artikel->photo)
                        <img src="{{ asset('storage/' . $artikel->photo) }}"
                            class="card-img-top p-4 rounded"
                            alt="{{ $artikel->title }}"
                            style="max-height:430px; width:auto; display:block;">
                    @endif

                    <div class="card-body p-4">
                        {{-- Judul --}}
                        <h1 class="fw-bold">{{ $artikel->title }}</h1>

                        {{-- Info Penulis + Tanggal --}}
                        <p class="text-muted">
                            By: {{ $artikel->author_name ?? 'Unknown' }}
                            @if($artikel->created_at)
                                | {{ \Carbon\Carbon::parse($artikel->created_at)->format('d M Y') }}
                            @endif
                        </p>

                        <hr>

                        {{-- Konten --}}
                        <div class="artikel-content">
                            {!! $artikel->content !!}
                        </div>

                        <hr>

                        {{-- Statistik --}}
                        <p class="mt-3 text-muted article-stats">
                            👁 {{ $artikel->total_views }} views |

                            {{-- Likes --}}
                            @auth
                                <a href="javascript:void(0)"
                                class="like-btn"
                                data-type="article"
                                data-id="{{ $artikel->article_id }}">
                                    ❤ <span class="like-count">{{ $artikel->total_likes ?? 0 }}</span> likes
                                </a>
                            @else
                                ❤ <span>{{ $artikel->total_likes }}</span> likes
                            @endauth

                            |

                            {{-- Comments --}}
                            @auth
                                <a href="#comment-section">💬 <span id="total-comments">{{ $artikel->activeComments()->count() }}</span> comments</a>
                            @else
                                💬 <span id="total-comments">{{ $artikel->activeComments()->count() }}</span> comments
                            @endauth
                        </p>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR ARTIKEL LAINNYA --}}
            <div class="col-md-4">
                <div class="card card-artikel shadow-sm border-0" style="background: linear-gradient(rgba(179, 170, 255, 0.75), rgba(255, 197, 237, 0.75));">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Artikel lainnya</h5>

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
                                                style="height:80px; width:100%; object-fit: cover;">
                                        </div>
                                    @endif
                                    <div class="col-8">
                                        <div class="card-body py-2 px-3">
                                            <h6 class="card-title mb-1 fw-semibold">{{ Str::limit($lain->title, 50) }}</h6>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($lain->created_at)->format('d M Y') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p class="text-muted">Belum ada artikel lainnya.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <hr>

         {{-- Pengingat sebelum berkomentar --}}
        @auth
        <div class="border-0 shadow-sm rounded-3 p-3 mb-4" style="background-color:#e6f4ea;">
            <strong>Ingat Sebelum Berkomentar</strong><br>
            Mari bangun forum yang aman, sehat, dan nyaman untuk semua dengan sama-sama menghindari:
            <ul class="mt-2 mb-2">
                <li>❌ Komentar yang menghina, merendahkan, atau menyerang pribadi.</li>
                <li>❌ Kata-kata kasar, rasis, atau diskriminatif.</li>
                <li>❌ Menyebarkan gosip atau informasi pribadi orang lain.</li>
            </ul>
            <span>Setiap kata yang kita tulis bisa berdampak – pastikan dampaknya positif.</span>
        </div>

        {{-- Custom Comment Section --}}
        <div class="mt-4" id="comment-section">

            {{-- Comment Form --}}
            @auth
            <div class="card card-artikel shadow-sm border-0 mb-4" style="background: rgba(167, 155, 233, 0.85);">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Tulis Komentar</h5>
                    <form id="comment-form">
                        @csrf
                        <div class="d-flex gap-3">
                            <img src="{{ Auth::user()->siswa ? (Auth::user()->siswa->photo ? asset('storage/' . Auth::user()->siswa->photo) : '/images/default-avatar.png') : (Auth::user()->counselorProfile ?
                            (Auth::user()->counselorProfile->photo ? asset('storage/' . Auth::user()->counselorProfile->photo) : '/images/default-avatar.png') : '/images/default-avatar.png') }}"
                                alt="{{ Auth::user()->name }}"
                                class="rounded-circle"
                                width="40" height="40"
                                style="object-fit: cover;">
                            <div class="flex-grow-1">
                                <textarea id="comment-text" name="comment_text" class="form-control border-0 bg-light"
                                        rows="3" placeholder="Bagikan pemikiran Anda..." required
                                        style="resize: none;"></textarea>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-muted">Gunakan bahasa yang sopan dan membangun</small>
                                    <button type="submit" class="dt dt-btn create">
                                        <span class="submit-text">Kirim Komentar</span>
                                        <div class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="card card-artikel shadow-sm border-0 mb-4">
                <div class="card-body text-center">
                    <p class="mb-3">Silakan <a href="{{ route('public.login') }}">login</a> untuk berkomentar</p>
                </div>
            </div>
            @endauth

            {{-- Comments List --}}
            <div class="card card-artikel shadow-sm border-0" style="background: linear-gradient(rgba(167, 155, 233, 0.85), rgba(245, 186, 222, 0.85));">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">
                        Komentar (<span id="total-comments">{{ $artikel->activeComments()->count() ?? 0 }}</span>)
                    </h5>

                    <div id="comments-container">
                        {{-- Comments will be loaded here --}}
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Memuat komentar...</span>
                            </div>
                        </div>
                    </div>

                    {{-- Load More Comments Button --}}
                    <div id="load-more-container" class="text-center mt-4 d-none">
                        <button id="load-more-comments" class="btn btn-outline-secondary" data-page="2">
                            <span class="load-text">Muat Lebih Banyak</span>
                            <div class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Report Modal --}}
        <div class="modal fade" id="reportModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Laporkan Konten</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="report-form">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" id="report-target-type" name="target_type">
                            <input type="hidden" id="report-target-id" name="target_id">

                            <div class="mb-3">
                                <label for="report-reason" class="form-label">Alasan Laporan</label>
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
                                <label for="custom-reason-text" class="form-label">Jelaskan alasan Anda</label>
                                <textarea class="form-control" id="custom-reason-text" rows="3"
                                        placeholder="Tuliskan alasan laporan Anda..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">
                                <span class="submit-text">Kirim Laporan</span>
                                <div class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endauth
    </div>
@endsection

@push('scripts')
<script>
// Global variables
let commentsLoaded = false;
let loadInitialCommentsFunc = null;
let isLoadingReplies = false; // Prevent multiple simultaneous loads

document.addEventListener("DOMContentLoaded", function() {
    // Simple, controlled highlighting function
    function highlightCommentFromHash() {
        if (!window.location.hash || !window.location.hash.startsWith('#comment-')) {
            return;
        }
        
        const commentId = window.location.hash.replace('#comment-', '');
        console.log('Looking for comment:', commentId);
        
        function tryHighlight() {
            const el = document.querySelector('#comment-' + commentId);
            
            if (el) {
                console.log('Found element, highlighting');
                setTimeout(function() {
                    el.scrollIntoView({ behavior: "smooth", block: "center" });
                    el.classList.add("highlight-comment");
                    
                    setTimeout(function() {
                        el.style.transition = 'all 1s ease';
                        el.classList.remove("highlight-comment");
                    }, 3000);
                }, 300);
                return true;
            }
            return false;
        }
        
        // If comments not loaded, load them first
        if (!commentsLoaded) {
            console.log('Loading comments...');
            if (loadInitialCommentsFunc) {
                loadInitialCommentsFunc();
            }
            
            // Simple polling - just look for the element
            let attempts = 0;
            const checkInterval = setInterval(function() {
                attempts++;
                
                if (tryHighlight()) {
                    clearInterval(checkInterval);
                } else if (attempts >= 50) {
                    clearInterval(checkInterval);
                    console.log('Comment not found in main comments');
                }
            }, 100);
        } else {
            tryHighlight();
        }
    }
    
    highlightCommentFromHash();
    window.addEventListener('hashchange', highlightCommentFromHash);
});

$(document).ready(function() {
    const articleId = {{ $artikel->article_id }};
    let isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};

    function setLoading(element, loading) {
        const $element = $(element);
        if (loading) {
            $element.addClass('loading');
            $element.find('.submit-text, .load-text, .replies-text').addClass('d-none');
            $element.find('.spinner-border').removeClass('d-none');
        } else {
            $element.removeClass('loading');
            $element.find('.submit-text, .load-text, .replies-text').removeClass('d-none');
            $element.find('.spinner-border').addClass('d-none');
        }
    }

    function loadInitialComments() {
        if (commentsLoaded) return;

        $.ajax({
            url: `/serenity/comments/${articleId}/load-more`,
            method: 'GET',
            data: { page: 1 },
            success: function(response) {
                commentsLoaded = true;

                if (response.html) {
                    $('#comments-container').html(response.html);

                    if (response.has_more) {
                        $('#load-more-container').removeClass('d-none');
                        $('#load-more-comments').data('page', response.next_page);
                    }
                } else {
                    $('#comments-container').html(`
                        <div id="no-comments" class="text-center text-muted py-4">
                            <i class="bi bi-chat-dots fs-1 opacity-50"></i>
                            <p class="mt-2">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                $('#comments-container').html(`
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                        <p class="mt-2">Gagal memuat komentar. <a href="#" onclick="location.reload()">Muat ulang halaman</a></p>
                    </div>
                `);
            }
        });
    }

    loadInitialCommentsFunc = loadInitialComments;

    function checkIfShouldLoadComments() {
        const commentSection = document.getElementById('comment-section');
        if (commentSection) {
            const rect = commentSection.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom > 0) {
                loadInitialComments();
            }
        }
    }

    checkIfShouldLoadComments();
    $(window).on('scroll', checkIfShouldLoadComments);

   // Submit Comment (for main comments)
    $(document).on('submit', '#comment-form', function(e) {
        e.preventDefault();

        if (!isAuthenticated) {
            showAlert('Silakan login terlebih dahulu untuk berkomentar.', 'warning');
            return;
        }

        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');
        const commentText = $('#comment-text').val().trim();

        if (!commentText) {
            showAlert('Komentar tidak boleh kosong.', 'warning');
            return;
        }

        setLoading($submitBtn, true);

        $.ajax({
            url: `/serenity/comments/${articleId}`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                comment_text: commentText
            },
            success: function(response) {
                $('#comment-text').val('');

                $('#no-comments').remove();

                $('#comments-container').prepend(response.html);

                const currentCount = parseInt($('#total-comments').text());
                $('#total-comments').text(currentCount + 1);

                $('#comments-container .comment-item:first').addClass('fade-in');

                showAlert('Komentar berhasil ditambahkan!');
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan saat menambahkan komentar.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showAlert(message, 'danger');
            },
            complete: function() {
                setLoading($submitBtn, false);
            }
        });
    });

    // Submit Reply (handles nested replies) - FIXED VERSION
$(document).on('submit', '.reply-form', function(e) {
    e.preventDefault();

    if (!isAuthenticated) {
        showAlert('Silakan login terlebih dahulu untuk membalas.', 'warning');
        return;
    }

    const $form = $(this);
    const $submitBtn = $form.find('button[type="submit"]');
    const replyText = $form.find('textarea[name="reply_text"]').val().trim();
    const parentCommentId = $form.find('input[name="comment_id"]').val();

    if (!replyText) {
        showAlert('Balasan tidak boleh kosong.', 'warning');
        return;
    }

    setLoading($submitBtn, true);

    $.ajax({
        url: '/serenity/comment-replies',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            comment_id: parentCommentId,
            reply_text: replyText
        },
        success: function(response) {
            try {
                console.log('Reply response:', response);

                // Clear form and hide collapse
                $form.find('textarea').val('');
                $form.closest('.collapse').collapse('hide');

                const $parentComment = $form.closest('.comment-item');

                // Add new reply after the parent comment (this should work based on your backend)
                $parentComment.after(response.html);

                // Now we need to update or create the "Load Replies" button
                let $loadRepliesBtn = $parentComment.find('.load-replies-btn').first();
                const $actionsContainer = $parentComment.find('.d-flex.align-items-center.mb-3').first();

                if ($loadRepliesBtn.length > 0) {
                    // Update existing button
                    const currentCount = parseInt($loadRepliesBtn.data('replies-count')) || 0;
                    const newCount = currentCount + 1;
                    $loadRepliesBtn.data('replies-count', newCount);
                    $loadRepliesBtn.find('.replies-text').text(`${newCount} balasan`);

                    console.log('Updated existing replies button, new count:', newCount);
                } else {
                    // Create new load replies button
                    console.log('Creating new load replies button for comment:', parentCommentId);

                    const level = parseInt($parentComment.css('margin-left')) / 30 || 0;
                    const nextLevel = level + 1;

                    const newButtonHtml = `
                        <button class="btn btn-sm btn-link text-muted p-0 me-3 load-replies-btn"
                                data-comment-id="${parentCommentId}"
                                data-replies-count="1"
                                data-level="${nextLevel}">
                            <i class="bi bi-chat me-1"></i>
                            <span class="replies-text">1 balasan</span>
                            <div class="spinner-border spinner-border-sm d-none ms-1" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
                    `;

                    // Find the right place to insert the button
                    const $replyButton = $actionsContainer.find('button[data-bs-target*="replyForm"]');

                    if ($replyButton.length > 0) {
                        // Insert after the reply button
                        $replyButton.after(newButtonHtml);
                        console.log('Inserted button after reply button');
                    } else {
                        // Insert after the like button
                        const $likeButton = $actionsContainer.find('.like-btn');
                        if ($likeButton.length > 0) {
                            $likeButton.after(newButtonHtml);
                            console.log('Inserted button after like button');
                        } else {
                            // Fallback: append to actions container
                            $actionsContainer.append(newButtonHtml);
                            console.log('Appended button to actions container');
                        }
                    }

                    // Verify the button was added
                    $loadRepliesBtn = $parentComment.find('.load-replies-btn').first();
                    if ($loadRepliesBtn.length > 0) {
                        console.log('New load replies button successfully created');
                    } else {
                        console.error('Failed to create load replies button');
                    }
                }

                // Ensure the replies container exists and is visible for future use
                let $repliesContainer = $parentComment.find('.replies-container').first();
                if ($repliesContainer.length === 0) {
                    // Create replies container if it doesn't exist
                    const repliesContainerHtml = `
                        <div class="replies-container d-none mt-3" id="replies-${parentCommentId}">
                            <div class="replies-list"></div>
                            <div class="load-more-replies-container mt-2"></div>
                        </div>
                    `;
                    $parentComment.find('.card-body').append(repliesContainerHtml);
                    $repliesContainer = $parentComment.find('.replies-container').first();
                }

                // Add animation to new reply
                const $newReply = $parentComment.next('.comment-item');
                if ($newReply.length > 0) {
                    $newReply.addClass('fade-in');
                }

                showAlert('Balasan berhasil ditambahkan!');

            } catch (error) {
                console.error('Error in reply success handler:', error);
                console.error('Error stack:', error.stack);
                // Still show success message since the reply was actually saved
                showAlert('Balasan berhasil ditambahkan!');
            }
        },
        error: function(xhr) {
            console.error('Reply submission error:', xhr);
            console.error('Status:', xhr.status);
            console.error('Response:', xhr.responseText);

            let message = 'Terjadi kesalahan saat menambahkan balasan.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showAlert(message, 'danger');
        },
        complete: function() {
            setLoading($submitBtn, false);
        }
    });
});

$(document).on('click', '.load-replies-btn', function(e) {
        e.preventDefault();

        const $btn = $(this);
        const commentId = $btn.data('comment-id');
        const level = $btn.data('level') || 1;
        const $commentCard = $btn.closest('.comment-item');
        const $repliesContainer = $commentCard.find('.replies-container').first();

        if ($repliesContainer.hasClass('d-none')) {
            $repliesContainer.removeClass('d-none');
            $btn.find('.replies-text').text('Sembunyikan balasan');

            const existingReplies = $(`.comment-item[data-parent-id="${commentId}"]`);

            if (existingReplies.length === 0) {
                // Prevent multiple clicks
                if ($btn.hasClass('loading')) return;
                
                setLoading($btn, true);

                $.ajax({
                    url: `/serenity/comment-replies/${commentId}/load-replies`,
                    method: 'GET',
                    data: { page: 1, level: level },
                    success: function(response) {
                        if (response.html) {
                            $commentCard.after(response.html);

                            // Check if we just loaded the comment we're looking for
                            if (window.location.hash && window.location.hash.startsWith('#comment-')) {
                                const targetId = window.location.hash.replace('#comment-', '');
                                const targetEl = document.querySelector('#comment-' + targetId);
                                
                                if (targetEl) {
                                    setTimeout(function() {
                                        targetEl.scrollIntoView({ behavior: "smooth", block: "center" });
                                        targetEl.classList.add("highlight-comment");
                                        
                                        setTimeout(function() {
                                            targetEl.style.transition = 'all 1s ease';
                                            targetEl.classList.remove("highlight-comment");
                                        }, 3000);
                                    }, 300);
                                }
                            }

                            if (response.has_more) {
                                $repliesContainer.find('.load-more-replies-container').html(`
                                    <button class="btn btn-link btn-sm load-more-replies p-0 text-muted"
                                             data-comment-id="${commentId}"
                                             data-page="${response.next_page}"
                                             data-level="${level}">
                                         Lihat ${response.remaining_count} balasan lainnya
                                         <div class="spinner-border spinner-border-sm d-none ms-1" role="status">
                                             <span class="visually-hidden">Loading...</span>
                                         </div>
                                     </button>
                                `);
                            }
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Gagal memuat balasan.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showAlert(errorMessage, 'danger');
                    },
                    complete: function() {
                        setLoading($btn, false);
                    }
                });
            } else {
                existingReplies.show();
            }
        } else {
            hideAllRepliesForComment(commentId);
            $repliesContainer.addClass('d-none');
            const repliesCount = $btn.data('replies-count');
            $btn.find('.replies-text').text(`${repliesCount} balasan`);
        }
    });

// Function to recursively hide all replies for a comment
function hideAllRepliesForComment(parentCommentId) {
    // Find all direct replies to this comment
    const directReplies = $(`.comment-item[data-parent-id="${parentCommentId}"]`);

    directReplies.each(function() {
        const replyId = $(this).data('comment-id');

        // Recursively hide replies to this reply
        hideAllRepliesForComment(replyId);

        // Hide this reply
        $(this).hide();
    });
}

// Load More Replies - IMPROVED VERSION
$(document).on('click', '.load-more-replies', function(e) {
    e.preventDefault();

    const $btn = $(this);
    const commentId = $btn.data('comment-id');
    const page = $btn.data('page');
    const level = $btn.data('level');

    console.log('Loading more replies for comment:', commentId, 'page:', page, 'level:', level);

    setLoading($btn, true);

    $.ajax({
        url: `/serenity/comment-replies/${commentId}/load-replies`,
        method: 'GET',
        data: {
            page: page,
            level: level
        },
        success: function(response) {
            console.log('Load more replies response:', response);

            if (response.html) {
                // Find the last reply for this comment and insert after it
                const $parentComment = $(`.comment-item[data-comment-id="${commentId}"]`);
                let $insertAfter = $parentComment;

                // Find the last visible reply that belongs to this parent
                $(`.comment-item[data-parent-id="${commentId}"]`).each(function() {
                    if ($(this).is(':visible')) {
                        $insertAfter = $(this);
                    }
                });

                // Also check for nested replies
                let $lastElement = $insertAfter;
                let keepLooking = true;

                while (keepLooking) {
                    const $next = $lastElement.next('.comment-item');
                    if ($next.length > 0 && $next.is(':visible')) {
                        const nextMargin = parseInt($next.css('margin-left')) || 0;
                        const currentMargin = parseInt($lastElement.css('margin-left')) || 0;
                        if (nextMargin > currentMargin) {
                            $lastElement = $next;
                        } else {
                            keepLooking = false;
                        }
                    } else {
                        keepLooking = false;
                    }
                }

                $lastElement.after(response.html);

                if (response.has_more) {
                    $btn.data('page', response.next_page);
                    $btn.html(`
                        Lihat ${response.remaining_count} balasan lainnya
                        <div class="spinner-border spinner-border-sm d-none ms-1" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    `);
                    setLoading($btn, false);
                } else {
                    $btn.remove();
                }
            } else {
                $btn.remove();
            }
        },
        error: function(xhr) {
            console.error('Error loading more replies:', xhr);
            console.error('Status:', xhr.status);
            console.error('Response:', xhr.responseText);

            let errorMessage = 'Gagal memuat balasan.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                errorMessage = 'Komentar tidak ditemukan.';
            } else if (xhr.status === 500) {
                errorMessage = 'Terjadi kesalahan server. Silakan coba lagi.';
            }

            showAlert(errorMessage, 'danger');
            setLoading($btn, false);
        }
    });
});

    // Like/Unlike functionality
    $(document).on('click', '.like-btn', function(e) {
        e.preventDefault();

        if (!isAuthenticated) {
            showAlert('Silakan login terlebih dahulu untuk memberikan like.', 'warning');
            return;
        }

        const $btn = $(this);
        const targetType = $btn.data('type');
        const targetId = $btn.data('id');
        const $countSpan = $btn.find('.like-count');
        const $heartIcon = $btn.find('.bi-heart, .bi-heart-fill');

        // Prevent multiple clicks
        if ($btn.hasClass('loading')) return;

        setLoading($btn, true);

        $.ajax({
            url: '/likes/toggle',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                target_type: targetType,
                target_id: targetId
            },
            success: function(response) {
                if (response.status === 'ok') {
                    $countSpan.text(response.total_likes);

                    if (response.liked) {
                        $btn.addClass('liked');
                        $heartIcon.removeClass('bi-heart').addClass('bi-heart-fill text-danger');
                    } else {
                        $btn.removeClass('liked');
                        $heartIcon.removeClass('bi-heart-fill text-danger').addClass('bi-heart');
                    }
                }
            },
            error: function(xhr) {
                showAlert('Terjadi kesalahan saat memberikan like.', 'danger');
            },
            complete: function() {
                setLoading($btn, false);
            }
        });
    });

    // Delete Comment/Reply
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();

        const $btn = $(this);
        const targetType = $btn.data('type');
        const targetId = $btn.data('id');
        const targetName = targetType === 'comment' ? 'komentar' : 'balasan';

        showConfirm(`Yakin ingin menghapus ${targetName} ini?`, function (){
            setLoading($btn, true);

            const deleteUrl = targetType === 'comment'
                ? `/serenity/comments/${targetId}`
                : `/serenity/replies/${targetId}`;

            $.ajax({
                url: deleteUrl,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'ok') {
                        // Remove the element with animation
                        const $target = targetType === 'comment'
                            ? $btn.closest('.comment-item')
                            : $btn.closest('.reply-item');

                        $target.fadeOut(300, function() {
                            $(this).remove();

                            // Update comment count if it's a comment
                            if (targetType === 'comment') {
                                const currentCount = parseInt($('#total-comments').text());
                                $('#total-comments').text(Math.max(0, currentCount - 1));

                                // Show no comments message if needed
                                if ($('#comments-container .comment-item').length === 0) {
                                    $('#comments-container').html(`
                                        <div id="no-comments" class="text-center text-muted py-4">
                                            <i class="bi bi-chat-dots fs-1 opacity-50"></i>
                                            <p class="mt-2">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                                        </div>
                                    `);
                                }
                            } else {
                                // Update reply count for the parent comment
                                const $parentComment = $target.closest('.comment-item');
                                const $loadRepliesBtn = $parentComment.find('.load-replies-btn');
                                if ($loadRepliesBtn.length) {
                                    const currentCount = parseInt($loadRepliesBtn.data('replies-count')) || 0;
                                    const newCount = Math.max(0, currentCount - 1);
                                    $loadRepliesBtn.data('replies-count', newCount);

                                    if (newCount === 0) {
                                        $loadRepliesBtn.remove();
                                        $parentComment.find('.replies-container').addClass('d-none');
                                    } else {
                                        $loadRepliesBtn.find('.replies-text').text(`${newCount} balasan`);
                                    }
                                }
                            }
                        });

                        showAlert(`${targetName.charAt(0).toUpperCase() + targetName.slice(1)} berhasil dihapus.`);
                    }
                },
                error: function(xhr) {
                    let message = `Terjadi kesalahan saat menghapus ${targetName}.`;
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showAlert(message, 'danger');
                },
                complete: function() {
                    setLoading($btn, false);
                }
            });
        });
    });

    // Report functionality
    $(document).on('click', '.report-btn', function(e) {
        e.preventDefault();

        const targetType = $(this).data('type');
        const targetId = $(this).data('id');

        $('#report-target-type').val(targetType);
        $('#report-target-id').val(targetId);

        // Reset form
        $('#report-form')[0].reset();
        $('#custom-reason').addClass('d-none');

        $('#reportModal').modal('show');
    });

    // Handle report reason change
    $('#report-reason').on('change', function() {
        if ($(this).val() === 'Lainnya') {
            $('#custom-reason').removeClass('d-none');
            $('#custom-reason-text').prop('required', true);
        } else {
            $('#custom-reason').addClass('d-none');
            $('#custom-reason-text').prop('required', false).val('');
        }
    });

    // Submit Report
    $('#report-form').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');

        setLoading($submitBtn, true);

        const reason = $('#report-reason').val() === 'Lainnya'
            ? $('#custom-reason-text').val().trim()
            : $('#report-reason').val();

        if (!reason) {
            showAlert('Harap pilih atau tuliskan alasan laporan.', 'warning');
            setLoading($submitBtn, false);
            return;
        }

        const formData = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            target_type: $('#report-target-type').val(),
            target_id: $('#report-target-id').val(),
            reason: reason
        };

        // Add the appropriate ID field based on target type
        if (formData.target_type === 'comment') {
            formData.comment_id = formData.target_id;
        } else if (formData.target_type === 'reply') {
            formData.reply_id = formData.target_id;
        }

        $.ajax({
            url: '/serenity/comment-reports',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#reportModal').modal('hide');
                showAlert('Laporan berhasil dikirim. Tim kami akan meninjau laporan Anda.');
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan saat mengirim laporan.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showAlert(message, 'danger');
            },
            complete: function() {
                setLoading($submitBtn, false);
            }
        });
    });

    // Load More Comments
    $(document).on('click', '#load-more-comments', function(e) {
        e.preventDefault();

        const $btn = $(this);
        const page = $btn.data('page');

        setLoading($btn, true);

        $.ajax({
            url: `/serenity/comments/${articleId}/load-more`,
            method: 'GET',
            data: { page: page },
            success: function(response) {
                if (response.html) {
                    $('#comments-container').append(response.html);

                    if (response.has_more) {
                        $btn.data('page', response.next_page);
                    } else {
                        $('#load-more-container').remove();
                    }

                    // Add fade-in animation to new comments
                    setTimeout(() => {
                        $('#comments-container .comment-item:nth-last-child(-n+' + response.count + ')').addClass('fade-in');
                    }, 50);
                }
            },
            error: function(xhr) {
                console.error('Load more comments error:', xhr);
                showAlert('Terjadi kesalahan saat memuat komentar.', 'danger');
            },
            complete: function() {
                setLoading($btn, false);
            }
        });
    });
});
</script>
@endpush
