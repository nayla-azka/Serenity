{{-- resources/views/public/partials/reply.blade.php --}}

<div class="reply-item border-start border-2 ps-3 mb-2" data-reply-id="{{ $reply->comment_id }}">
    <div class="d-flex justify-content-between align-items-start">
        <div class="flex-grow-1">
            <div class="d-flex align-items-center mb-1">
                @if($reply->user)
                    <img class="me-2 rounded-circle" 
                         src="{{ $reply->user->siswa ? 
                         ($reply->user->siswa->photo ? asset('storage/' . $reply->user->siswa->photo) : '/images/default-avatar.png') : ($reply->user->counselorProfile ? 
                         ($reply->user->counselorProfile->photo ? asset('storage/' . $reply->user->counselorProfile->photo) : '/images/default-avatar.png') : '/images/default-avatar.png') }}"
                         alt="{{ $reply->user->name }}"
                         width="24" height="24"
                         style="object-fit: cover;">
                    <strong class="me-2">{{ $reply->user->name }}</strong>
                @else
                    <img class="me-2 rounded-circle" 
                         src="/images/default-avatar.png"
                         alt="Anonim"
                         width="24" height="24">
                    <strong class="me-2">Anonim</strong>
                @endif
                <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
            </div>
            <p class="mb-2 text-break">{{ $reply->comment_text }}</p>
        </div>
        
        <!-- Dropdown Actions -->
        @auth
        <div class="dropdown">
            <button class="btn btn-sm btn-link text-muted p-1" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @if(Auth::id() !== $reply->user_id)
                    {{-- Report --}}
                    <li>
                        <a href="#" class="dropdown-item report-btn" 
                           data-type="reply" 
                           data-id="{{ $reply->comment_id }}">
                            <i class="bi bi-flag me-2"></i>Laporkan
                        </a>
                    </li>
                @endif

                {{-- Delete (owner, admin, konselor) --}}
                @if(Auth::id() === $reply->user_id || 
                    in_array(Auth::user()->role ?? '', ['admin', 'konselor']))
                    <li>
                        <a href="#" class="dropdown-item text-danger delete-btn"
                           data-type="reply" 
                           data-id="{{ $reply->comment_id }}">
                            <i class="bi bi-trash me-2"></i>Hapus
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        @endauth
    </div>

    <!-- Reply Actions -->
    <div class="d-flex align-items-center">
        @auth
            <button type="button"
                    class="btn btn-sm btn-link like-btn p-0 me-3 text-muted {{ ($reply->liked_by_user ?? false) ? 'liked' : '' }}"
                    data-type="comment"
                    data-id="{{ $reply->comment_id }}">
                <i class="bi bi-heart{{ ($reply->liked_by_user ?? false) ? '-fill text-danger' : '' }} me-1"></i>
                <span class="like-count">{{ $reply->likes_count ?? 0 }}</span>
                <div class="spinner-border spinner-border-sm d-none ms-1" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </button>
        @else
            <span class="btn btn-sm btn-link p-0 me-3 text-muted">
                <i class="bi bi-heart me-1"></i>
                <span class="like-count">{{ $reply->likes_count ?? 0 }}</span>
            </span>
        @endauth
    </div>
</div>