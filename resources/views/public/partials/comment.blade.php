{{-- resources/views/public/partials/comment.blade.php --}}

<style>
    .btn-link {
        color: inherit !important;
        text-decoration: none !important;
        font-weight: normal;
    }
    .btn-link:hover {
        color: inherit !important;
        text-decoration: none !important;
    }

    .like-btn.liked {
        color: #0d6efd !important;
        font-weight: bold;
    }
    .like-btn.liked:hover {
        opacity: 0.8;
    }
</style>

{{-- resources/views/public/partials/comment.blade.php --}}

<div class="card mb-3 comment-item"
    id="comment-{{ $comment->comment_id }}"
    data-comment-id="{{ $comment->comment_id }}"
    data-parent-id="{{ $comment->parent_id ?? '' }}"
    style="margin-left: {{ ($level ?? 0) * 30 }}px;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-2">
                    @if($comment->user)
                        <img class="me-2 rounded-circle"
                             src="{{ $comment->user->siswa ?
                                ($comment->user->siswa->photo ? asset('storage/' . $comment->user->siswa->photo) : '/images/default-avatar.png') :
                                ($comment->user->counselorProfile ? ($comment->user->counselorProfile->photo ? asset('storage/' . $comment->user->counselorProfile->photo) : '/images/default-avatar.png') : '/images/default-avatar.png') }}"
                             alt="{{ $comment->user->name }}"
                             width="32" height="32"
                             style="object-fit: cover;">
                        <div>
                            <strong>{{ $comment->user->name }}</strong>
                            <small class="text-muted d-block">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                    @else
                        <img class="me-2 rounded-circle"
                             src="/images/default-avatar.png"
                             alt="Anonim"
                             width="32" height="32">
                        <div>
                            <strong>Anonim</strong>
                            <small class="text-muted d-block">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                    @endif
                </div>
                <p class="mb-3 text-break">{{ $comment->comment_text }}</p>
            </div>

            <!-- Dropdown Actions -->
            @auth
            <div class="dropdown">
                <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if(Auth::id() !== $comment->user_id)
                        {{-- Report --}}
                        <li>
                            <a href="#" class="dropdown-item report-btn"
                               data-type="comment"
                               data-id="{{ $comment->comment_id }}">
                                <i class="bi bi-flag me-2"></i>Laporkan
                            </a>
                        </li>
                    @endif

                    {{-- Delete (owner, admin, konselor) --}}
                    @if(Auth::id() === $comment->user_id ||
                        in_array(Auth::user()->role ?? '', ['admin', 'konselor']))
                        <li>
                            <a href="#" class="dropdown-item text-danger delete-btn"
                               data-type="comment"
                               data-id="{{ $comment->comment_id }}">
                                <i class="bi bi-trash me-2"></i>Hapus
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            @endauth
        </div>

        <!-- Comment Actions -->
        <div class="d-flex align-items-center mb-3">
            @auth
                <button type="button"
                        class="btn btn-sm btn-link like-btn p-0 me-3 text-muted {{ ($comment->liked_by_user ?? false) ? 'liked' : '' }}"
                        data-type="comment"
                        data-id="{{ $comment->comment_id }}">
                    <i class="bi bi-heart{{ ($comment->liked_by_user ?? false) ? '-fill text-danger' : '' }} me-1"></i>
                    <span class="like-count">{{ $comment->likes_count ?? 0 }}</span>
                    <div class="spinner-border spinner-border-sm d-none ms-1" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </button>

                {{-- Reply button (show only if nesting level is less than 5) --}}
                @if(($level ?? 0) < 5)
                    <button class="btn btn-sm btn-link text-muted p-0 me-3"
                            data-bs-toggle="collapse"
                            data-bs-target="#replyForm-{{ $comment->comment_id }}">
                        <i class="bi bi-reply me-1"></i>Balas
                    </button>
                @endif

                {{-- Show replies button if there are replies --}}
                @if($comment->replies_count > 0)
                    <button class="btn btn-sm btn-link text-muted p-0 load-replies-btn"
                            data-comment-id="{{ $comment->comment_id }}"
                            data-replies-count="{{ $comment->replies_count }}"
                            data-level="{{ ($level ?? 0) + 1 }}">
                        <i class="bi bi-chat me-1"></i>
                        <span class="replies-text">{{ $comment->replies_count }} balasan</span>
                        <div class="spinner-border spinner-border-sm d-none ms-1" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </button>
                @endif
            @else
                <span class="btn btn-sm btn-link p-0 me-3 text-muted">
                    <i class="bi bi-heart me-1"></i>
                    <span class="like-count">{{ $comment->likes_count ?? 0 }}</span>
                </span>
                {{-- Show replies count for guests --}}
                @if(($comment->replies_count ?? 0) > 0)
                    <span class="btn btn-sm btn-link p-0 text-muted">
                        <i class="bi bi-chat me-1"></i>
                        {{ $comment->replies_count }} balasan
                    </span>
                @endif
            @endauth
        </div>

        <!-- Reply form -->
        @auth
        @if(($level ?? 0) < 5)
        <div class="collapse" id="replyForm-{{ $comment->comment_id }}">
            <div class="border-top pt-3">
                <form class="reply-form">
                    @csrf
                    <input type="hidden" name="comment_id" value="{{ $comment->comment_id }}">
                    <div class="d-flex gap-2">
                        <img src="{{ Auth::user()->siswa ? (Auth::user()->siswa->photo ? asset('storage/' . Auth::user()->siswa->photo) : '/images/default-avatar.png') : (Auth::user()->counselorProfile ? (Auth::user()->counselorProfile->photo ? asset('storage/' . Auth::user()->counselorProfile->photo) : '/images/default-avatar.png') : '/images/default-avatar.png') }}"
                             alt="{{ Auth::user()->name }}"
                             class="rounded-circle"
                             width="24" height="24"
                             style="object-fit: cover;">
                        <div class="flex-grow-1">
                            <textarea name="reply_text" class="form-control border-0 bg-light"
                                      rows="2" placeholder="Tulis balasan..." required
                                      style="resize: none;"></textarea>
                            <div class="d-flex justify-content-end mt-2">
                                <button type="button" class="dt dt-btn serenity btn-link text-muted me-2"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#replyForm-{{ $comment->comment_id }}">
                                    Batal
                                </button>

                                <button type="submit" class="dt dt-btn create">
                                    <span class="submit-text">Kirim</span>
                                    <div class="spinner-border spinner-border-sm d-none ms-1" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
        @endauth

        <!-- Replies Container -->
        <div class="replies-container d-none mt-3" id="replies-{{ $comment->comment_id }}">
            <div class="replies-list"></div>
            <div class="load-more-replies-container mt-2"></div>
        </div>
    </div>
</div>
