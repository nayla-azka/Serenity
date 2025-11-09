@extends('admin.layouts.layout')

@push('styles')
<style>
    html, body {
        overflow: hidden;
    }
    
    .chat-container {
        margin: -1rem !important;
        height: calc(100vh - 120px);
        overflow: hidden;
    }

    .chat-bubble {
        border-radius: 1rem;
        max-width: 70%;
        word-break: break-word;
        overflow-wrap: break-word;
        animation: fadeInUp 0.3s ease;
        transition: background 0.2s;
        display: inline-block;
        padding: 0.75rem 1rem;
        margin: 0.25rem 0;
    }
    
    .chat-bubble:hover {
        background: rgba(0,0,0,0.05);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chat-bubble.sent {
        background: #837ab6;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 0.3rem;
    }
    
    .chat-bubble.received {
        background: #e9ecef;
        color: #333;
        align-self: flex-start;
        border-bottom-left-radius: 0.3rem;
    }

    .message-meta {
        font-size: 0.75rem;
        opacity: 0.7;
        margin-top: 0.25rem;
    }

    .date-divider {
        text-align: center;
        margin: 1rem 0;
        position: relative;
    }

    .date-divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #dee2e6;
    }

    .date-divider span {
        background: #fafafa;
        padding: 0.5rem 1rem;
        color: #6c757d;
        font-size: 0.8rem;
        position: relative;
        border-radius: 15px;
        border: 1px solid #dee2e6;
    }

    #chat-box::-webkit-scrollbar {
        width: 6px;
    }
    
    #chat-box::-webkit-scrollbar-thumb {
        background: rgba(131,122,182,0.6);
        border-radius: 3px;
    }
    
    #chat-box::-webkit-scrollbar-thumb:hover {
        background: rgba(131,122,182,1);
    }

    .quick-reply-btn {
        transition: all 0.2s ease;
    }
    
    .quick-reply-btn:hover {
        background-color: #837ab6 !important;
        color: white !important;
        transform: scale(1.05);
    }

    .session-item {
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    
    .session-item:hover {
        background-color: rgba(131,122,182,0.1) !important;
        transform: translateY(-3px);
    }
    
    .session-item.active {
        background-color: rgba(131,122,182,0.08) !important;
    }

    .session-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 3px;
        height: 100%;
        background: linear-gradient(135deg, #837ab6, #9f95d3);
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }

    .session-item:hover::before,
    .session-item.active::before {
        transform: translateX(0);
    }

    .chat-header {
        background: linear-gradient(90deg, #837ab6, #9f95d3);
        color: white;
    }
    
    .chat-header h6,
    .chat-header small {
        color: white !important;
    }

    .session-ended-banner {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        padding: 1rem;
        text-align: center;
        margin: -1rem -1rem 1rem -1rem;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        animation: slideDown 0.5s ease-out;
    }

    .session-ended-banner .badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        margin-bottom: 0.5rem;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .ended-session-footer {
        background: #f8f9fa;
        border-top: 2px solid #dee2e6;
        padding: 2rem;
        text-align: center;
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .session-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .action-btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .action-btn.archive {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
    }

    .action-btn.delete {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }

    .action-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none !important;
        box-shadow: none !important;
    }

    .student-deleted-indicator {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: #212529;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        margin-top: 0.5rem;
        display: inline-block;
    }

    .student-archived-indicator {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        margin-top: 0.5rem;
        display: inline-block;
    }
    
    .dt-btn.send:hover {
        color: #fff;
        transform: scale(1.05);
        outline: 1.5px solid #a89ad9;
    }

    .dt-btn.send {
        display: inline-block;
        position: relative;
        overflow: hidden;
        color: #f8f6ff;
        background-color: rgb(131, 122, 182);
        transition: all 400ms;
        text-decoration: none;
    }

    .dt-btn.send::before {
        content: "";
        position: absolute;
        left: -40px;
        top: 0;
        width: 0;
        height: 100%;
        background-color: #6b61a4;
        transform: skewX(45deg);
        z-index: -1;
        transition: width 400ms;
    }

    .dt-btn.send:hover::before {
        width: 200%;
    }

    /* ==================== PROFILE PICTURE FIX ==================== */
    /* This ensures ALL profile pictures are perfectly circular */
    
    img.rounded-circle,
    .rounded-circle img,
    .session-item img.rounded-circle,
    .chat-header img.rounded-circle,
    .position-relative img.rounded-circle {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        min-height: 40px !important;
        max-width: 40px !important;
        max-height: 40px !important;
        aspect-ratio: 1 / 1 !important;
        object-fit: cover !important;
        border-radius: 50% !important;
        display: block !important;
        flex-shrink: 0 !important;
        overflow: hidden !important;
        border: 2px solid #fff !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        vertical-align: middle !important;
    }

    /* Container for profile picture with status indicator */
    .position-relative.me-3 {
        position: relative !important;
        display: inline-block !important;
        flex-shrink: 0 !important;
        margin-right: 0.75rem !important;
    }

    /* Status indicators - positioned absolutely relative to parent */
    .online-status,
    .offline-status {
        position: absolute !important;
        bottom: 2px !important;
        right: 2px !important;
        width: 12px !important;
        height: 12px !important;
        min-width: 12px !important;
        min-height: 12px !important;
        border-radius: 50% !important;
        border: 2px solid #fff !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;
        z-index: 2 !important;
    }

    .online-status {
        background-color: #28a745 !important;
        animation: pulse-green 2s infinite;
    }

    .offline-status {
        background-color: #6c757d !important;
    }

    @keyframes pulse-green {
        0%, 100% {
            box-shadow: 0 1px 3px rgba(0,0,0,0.2), 0 0 0 0 rgba(40, 167, 69, 0.7);
        }
        50% {
            box-shadow: 0 1px 3px rgba(0,0,0,0.2), 0 0 0 4px rgba(40, 167, 69, 0);
        }
    }

    /* ==================== SESSION LIST UI ELEMENTS ==================== */

    .username-truncate {
        display: inline-block;
        max-width: 160px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
    }

    .unread-badge {
        background: linear-gradient(135deg, #dc3545, #e74c3c) !important;
        color: white !important;
        border-radius: 50% !important;
        min-width: 22px !important;
        height: 22px !important;
        padding: 0 6px !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem !important;
        font-weight: bold !important;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4) !important;
        animation: pulse-badge 2s infinite;
        line-height: 1 !important;
        flex-shrink: 0 !important;
        position: absolute !important;
        right: 0 !important;
        top: 0 !important;
    }

    @keyframes pulse-badge {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.9;
        }
    }

    .session-preview {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.25rem;
        margin-bottom: 0.25rem;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 100%;
    }

    /* Session item layout fixes */
    .session-item .d-flex {
        align-items: flex-start;
        gap: 0;
    }

    .session-item .flex-grow-1 {
        min-width: 0;
        flex: 1;
    }

    .session-item .min-width-0 {
        min-width: 0 !important;
    }

    /* Stats styling */
    #total-sessions,
    #total-unread {
        font-size: 1.25rem;
        display: block;
        font-weight: 600;
    }

    /* Scrollbar for session list */
    .flex-grow-1::-webkit-scrollbar {
        width: 6px;
    }
    
    .flex-grow-1::-webkit-scrollbar-thumb {
        background: rgba(131,122,182,0.4);
        border-radius: 3px;
    }
    
    .flex-grow-1::-webkit-scrollbar-thumb:hover {
        background: rgba(131,122,182,0.7);
    }

    /* Fix for empty state */
    .text-center.text-muted.p-4 {
        padding: 2rem !important;
    }

    .text-center.text-muted.p-4 i {
        opacity: 0.5;
        margin-bottom: 1rem !important;
        display: block;
    }

    /* Archive button styling */
    .dt.dt-btn.create {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
        min-height: auto;
    }

    .dt.dt-btn.create a {
        color: inherit;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Header consistency */
    .border-bottom.flex-shrink-0 {
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Strong tags in session items */
    .session-item strong {
        font-weight: 600;
        color: #212529;
    }

    /* Session item padding and spacing */
    .session-item > div {
        position: relative;
    }

    .session-item .d-flex.align-items-center.justify-content-between {
        position: relative;
        min-height: 22px;
    }

    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        img.rounded-circle,
        .rounded-circle img,
        .position-relative img.rounded-circle {
            width: 35px !important;
            height: 35px !important;
            min-width: 35px !important;
            min-height: 35px !important;
            max-width: 35px !important;
            max-height: 35px !important;
        }

        .online-status,
        .offline-status {
            width: 10px !important;
            height: 10px !important;
            min-width: 10px !important;
            min-height: 10px !important;
        }

        .username-truncate {
            max-width: 120px;
        }

        .unread-badge {
            min-width: 20px !important;
            height: 20px !important;
            font-size: 0.65rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="chat-container">
    <div class="row h-100 g-0">
        <!-- Sidebar - Student Sessions -->
        <div class="col-3 border-end d-flex flex-column" style="background-color: #f8f9fa; height: calc(93vh - 60px); overflow: hidden;">
           <!-- Header -->
            <div class="p-3 border-bottom flex-shrink-0" style="background-color: rgb(131, 122, 182); color: white;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">
                            <i class="fas fa-comments me-2"></i>Chat Konseling
                        </h6>
                        <small class="opacity-75">{{ Auth::user()->name ?? 'Konselor' }}</small>
                    </div>
                    <button class="dt dt-btn create" type="button">
                        <a class="dropdown-item" href="{{ route('admin.konseling.archive-list') }}">
                            <i class="bi bi-archive"></i>
                        </a>
                    </button>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="p-2 bg-light border-bottom flex-shrink-0">
                <div class="row text-center">
                    <div class="col-6">
                        <small class="text-muted d-block">Total</small>
                        <strong id="total-sessions">{{ $allSessions->count() }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Belum Dibaca</small>
                        <strong id="total-unread" class="text-danger">
                            {{ $allSessions->sum('unread_count') }}
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Sessions List -->
            <div class="flex-grow-1" style="overflow-y: auto; overflow-x: hidden; height: 0;">
                @if($allSessions->count() > 0)
                    @foreach($allSessions as $s)
                    @php
                        $isSelected = isset($session) && $session->id_session == $s->id_session;
                        $studentName = $s->student->user->name ?? $s->student->student_name ?? 'Unknown Student';
                        $latestMsg = $s->latestMessage;
                        $preview = $latestMsg ? \Str::limit($latestMsg->message, 40) : 'Belum ada pesan';
                        $timeAgo = $latestMsg ? $latestMsg->sent_at->diffForHumans() : $s->created_at->diffForHumans();
                        $studentViewStatus = $s->getViewStatus('student', $s->id_student);
                    @endphp
                    <a href="{{ route('admin.konseling.show', $s->id_session) }}"
                       class="d-block text-decoration-none text-dark session-item {{ $isSelected ? 'active' : '' }}"
                       style="border-bottom: 1px solid #eee;">
                        <div class="p-3 position-relative">
                            <div class="d-flex align-items-start">
                                <div class="position-relative me-3">
                                    <img src="{{ $s->student->photo ? asset('storage/' . $s->student->photo) : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22%3E%3Crect width=%22100%25%22 height=%22100%25%22 fill=%22%23ddd%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3E👤%3C/text%3E%3C/svg%3E' }}"
                                         class="rounded-circle">
                                    @if($s->is_active)
                                        <div class="online-status"></div>
                                    @else
                                        <div class="offline-status"></div>
                                    @endif
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="d-flex align-items-center justify-content-between position-relative">
                                        <strong class="text-truncate">{{ $studentName }}</strong>
                                        @if($s->unread_count > 0)
                                            <span class="unread-badge">{{ $s->unread_count }}</span>
                                        @endif
                                    </div>
                                    <div class="session-preview">{{ $preview }}</div>
                                    <small class="text-muted">{{ $timeAgo }}</small>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                @else
                    <div class="text-center text-muted p-4">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>Belum ada percakapan</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Chat Area / Empty State -->
        <div class="col-9 d-flex flex-column" style="height: 100%; overflow:hidden;">
            @if(isset($session))
                @php
                    $studentName = $session->student->user->name ?? $session->student->student_name ?? 'Unknown Student';
                    $studentClass = $session->student->class->class_name ?? 'Unknown Class';
                    $studentViewStatus = $session->getViewStatus('student', $session->id_student);
                @endphp

                <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between flex-shrink-0">
                    <div class="d-flex align-items-center">
                        <img src="{{ $session->student->photo ? asset('storage/' . $session->student->photo) : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22%3E%3Crect width=%22100%25%22 height=%22100%25%22 fill=%22%23ddd%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3E👤%3C/text%3E%3C/svg%3E' }}"
                            class="rounded-circle object-fit-cover me-3" style="width: 40px; height: 40px;">
                        <div>
                            <h6 class="mb-0">{{ $studentName }}</h6>
                            <small class="text-muted">
                                {{ $studentClass }} • Topic: {{ $session->topic }}
                                @if($session->is_active)
                                    • Active Conversation
                                @else
                                    • Ended Session
                                @endif
                            </small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        @if($session->is_active)
                            <button class="btn btn-sm btn-outline-warning" onclick="endSession()">
                                <i class="fas fa-stop"></i> End Session
                            </button>
                        @else
                            <span class="badge bg-secondary fs-6">
                                <i class="fas fa-stop-circle me-1"></i>
                                Session Ended
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Session Ended Banner -->
                @if(!$session->is_active)
                    <div class="session-ended-banner">
                        <div class="badge bg-light text-dark mb-2">
                            <i class="fas fa-check-circle me-1"></i>
                            Session Completed
                        </div>
                        <p class="mb-0 small">You have successfully ended this counseling session with {{ $studentName }}.</p>
                    </div>
                @endif

                <!-- Chat Messages -->
                <div id="chat-box" class="flex-grow-1 p-3 overflow-auto" style="background:#fafafa;">
                    <div id="loading" class="text-center text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Loading messages...
                    </div>
                </div>

                <!-- Input Area or Ended Session Footer -->
                @if($session->is_active)
                    <!-- Active Session Input -->
                    <div class="p-3 border-top bg-white flex-shrink-0">
                        <form id="chat-form" class="d-flex mb-2">
                            @csrf
                            <input type="hidden" name="id_session" value="{{ $session->id_session }}">
                            <input type="text"
                                   name="message"
                                   class="form-control me-2"
                                   placeholder="Ketik balasan..."
                                   required
                                   maxlength="1000"
                                   autocomplete="off">
                            <button type="submit" class="dt dt-btn create send" id="send-btn" style="min-width: 60px;">
                                <span id="send-icon"><i class="fas fa-paper-plane"></i></span>
                                <span id="send-loading" style="display: none;">
                                    <div class="spinner-border spinner-border-sm"></div>
                                </span>
                            </button>
                        </form>

                        <!-- Quick Replies -->
                        <div class="d-flex flex-wrap gap-1">
                            <small class="text-muted me-2 align-self-center">Quick Replies:</small>
                            <button class="btn btn-sm btn-outline-secondary quick-reply-btn" onclick="insertQuickReply('Terima kasih sudah berbagi. Bagaimana perasaan Anda sekarang?')">
                                <i class="fas fa-heart"></i> Tanya Perasaan
                            </button>
                            <button class="btn btn-sm btn-outline-secondary quick-reply-btn" onclick="insertQuickReply('Saya memahami situasi Anda. Mari kita cari solusinya bersama.')">
                                <i class="fas fa-handshake"></i> Empati
                            </button>
                            <button class="btn btn-sm btn-outline-secondary quick-reply-btn" onclick="insertQuickReply('Apakah ada yang bisa saya bantu lebih lanjut?')">
                                <i class="fas fa-question-circle"></i> Tawarkan Bantuan
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Ended Session Footer -->
                    <div class="ended-session-footer flex-shrink-0">
                        <div class="mb-3">
                            <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                            <h5 class="text-muted mb-2">Session Completed</h5>
                            <p class="text-muted small mb-0">
                                This counseling session with {{ $studentName }} has been ended.<br>
                                <br>You can now archive or delete this session from your view.
                            </p>
                        </div>

                        <!-- Action buttons for ended sessions -->
                        <div class="session-actions justify-content-center">
                            @if(!$session->is_active)
                                <button class="action-btn archive" onclick="archiveSession({{ $session->id_session }})">
                                    <i class="fas fa-archive"></i>
                                    Archive Session
                                </button>

                                <button class="action-btn delete" onclick="deleteSession({{ $session->id_session }})">
                                    <i class="fas fa-trash"></i>
                                    Delete Session
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <br><br><br><br><br>
                <div class="d-flex flex-column align-items-center justify-content-center flex-grow-1">
                    <div class="text-center text-muted">
                        <div class="mb-3">
                            <i class="fas fa-comments fa-3x" style="color: rgb(131, 122, 182);"></i>
                        </div>
                        <h5>Panel Konseling Digital</h5>
                        <p>Pilih percakapan dari sidebar untuk memulai membalas siswa</p>
                        <div class="mt-4">
                            <div class="row justify-content-center g-3">
                                <div class="col-auto">
                                    <div class="card border-0" style="background-color: rgba(131, 122, 182, 0.1);">
                                        <div class="card-body text-center">
                                            <i class="fas fa-chart-line mb-2" style="color: rgb(131, 122, 182);"></i>
                                            <h6>Total Sesi</h6>
                                            <h4>{{ $allSessions->count() }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="card border-0" style="background-color: rgba(131, 122, 182, 0.1);">
                                        <div class="card-body text-center">
                                            <i class="fas fa-bell text-warning mb-2"></i>
                                            <h6>Belum Dibaca</h6>
                                            <h4 class="text-danger">{{ $allSessions->sum('unread_count') }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="card border-0" style="background-color: rgba(131, 122, 182, 0.1);">
                                        <div class="card-body text-center">
                                            <i class="fas fa-user-check text-success mb-2"></i>
                                            <h6>Aktif</h6>
                                            <h4>{{ $allSessions->where('is_active', true)->count() }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="card border-0" style="background-color: rgba(131, 122, 182, 0.1);">
                                        <div class="card-body text-center">
                                            <i class="bi bi-archive-fill text-primary mb-2"></i>
                                            <h6>Arsip</h6>
                                            <h4>{{ $archivedCount}}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(isset($session))
<script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.2.0/pusher.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ asset('js/chat-helper.js') }}"></script>

<script>
// FIXED: Use Laravel route helpers for admin/counselor routes
const adminChatRoutes = {
    send: "{{ route('admin.konseling.send') }}",
    fetch: function(sessionId) {
        return "{{ url('admin/konseling/fetch') }}/" + sessionId;
    },
    markRead: function(sessionId) {
        return "{{ url('admin/konseling/mark-read') }}/" + sessionId;
    },
    endSession: function(sessionId) {
        return "{{ url('admin/konseling/end') }}/" + sessionId;
    },
    archiveSession: function(sessionId) {
        return "{{ url('admin/konseling/archive') }}/" + sessionId;
    },
    deleteSession: function(sessionId) {
        return "{{ url('admin/konseling/delete') }}/" + sessionId;
    },
    stats: "{{ route('admin.konseling.stats') }}"
};

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

@if(isset($session) && $session->id_session)

$(document).ready(function() {
    console.log('Admin chat system initializing...');

    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    $.ajax({
        url: '/set-timezone',
        method: 'POST',
        data: { timezone: userTimezone },
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            console.log('Admin timezone stored in session:', userTimezone);
        },
        error: function(xhr) {
            console.warn('Failed to store admin timezone:', xhr.responseText);
        }
    });

    @if($session->is_active)
    const chatHelper = new ChatHelper();

    chatHelper.initializeChat({
        pusherKey: '{{ config("broadcasting.connections.pusher.key") }}',
        pusherCluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
        sessionId: {{ $session->id_session }},
        currentUserType: 'counselor',
        formSelector: '#chat-form',
        sendUrl: adminChatRoutes.send, // FIXED: Use route helper
        fetchUrl: adminChatRoutes.fetch({{ $session->id_session }}), // FIXED: Use route helper
        csrfToken: csrfToken,
        markAsReadUrl: adminChatRoutes.markRead({{ $session->id_session }}) // FIXED: Use route helper
    });

    if (chatHelper.channel) {
        chatHelper.channel.bind('message.sent', (data) => {
            console.log('📨 Admin received message:', data);
            
            if (data.sender_type === 'student') {
                chatHelper.appendMessage(data, 'counselor');
                
                // Update badge counts immediately
                updateUnreadCount();
                
                if (chatHelper.isPageVisible && chatHelper.isScrolledToBottom()) {
                    setTimeout(() => chatHelper.markMessagesAsRead(), 500);
                }
            }
        });
        
        chatHelper.channel.bind('messages.read', (data) => {
            console.log('📬 Admin received read receipt:', data);
            
            if (data.message_ids && Array.isArray(data.message_ids)) {
                chatHelper.updateMessageReadStatus(data.message_ids);
                
                // Update badges when student reads our messages
                updateUnreadCount();
            }
        });
    }

    $('#chat-form').off('submit').on('submit', function(e) {
        e.preventDefault();

        const messageInput = $('#chat-form input[name="message"]');
        const message = messageInput.val().trim();

        if (!message) {
            chatHelper.showError('Please enter a message');
            return;
        }

        chatHelper.setLoading(true);

        const data = {
            _token: csrfToken,
            id_session: {{ $session->id_session }},
            message: message,
            timezone: userTimezone
        };

        console.log('Sending admin message with data:', data);

        $.ajax({
            url: adminChatRoutes.send, // FIXED: Use route helper
            method: 'POST',
            data: data,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Admin message sent successfully:', response);

                let messageToAppend = response;
                if (response.messages && Array.isArray(response.messages)) {
                    messageToAppend = response.messages[0];
                }

                chatHelper.appendMessage(messageToAppend, 'counselor');

                messageInput.val('');
                chatHelper.setLoading(false);

                updateUnreadCount();
            },
            error: function(xhr, status, error) {
                console.error('Admin AJAX Error:', {
                    status: xhr.status,
                    response: xhr.responseText,
                    error: error
                });

                let errorMessage = 'Failed to send message';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        errorMessage = response.error;
                    } else if (response.errors) {
                        errorMessage = Object.values(response.errors).flat().join(', ');
                    }
                } catch (e) {
                    if (xhr.status === 500) {
                        errorMessage = 'Server error occurred. Please try again.';
                    } else if (xhr.status === 422) {
                        errorMessage = 'Invalid message data';
                    } else if (xhr.status === 403) {
                        errorMessage = 'You are not authorized to send messages';
                    } else if (xhr.status === 404) {
                        errorMessage = 'Session not found';
                    }
                }

                chatHelper.showError(errorMessage);
                chatHelper.setLoading(false);
            }
        });
    });

    @else
    loadEndedSessionMessages();
    @endif

    console.log('Admin chat system initialized successfully');
});

function loadEndedSessionMessages() {
    console.log('Loading ended session messages for admin...');

    $.ajax({
        url: adminChatRoutes.fetch({{ $session->id_session }}), // FIXED: Use route helper
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        success: function(messages) {
            $('#loading').hide();

            console.log('Admin loaded messages:', messages);

            if (Array.isArray(messages) && messages.length > 0) {
                const chatHelper = new ChatHelper();
                chatHelper.messageCache = new Set();

                messages.forEach(function(message) {
                    chatHelper.appendMessage(message, 'counselor');
                });

                chatHelper.scrollToBottom();
            } else {
                $('#chat-box').append('<div class="text-center text-muted"><em>No messages in this session.</em></div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to load admin messages:', {
                status: xhr.status,
                response: xhr.responseText,
                error: error
            });

            $('#loading').hide();
            $('#chat-box').append('<div class="text-center text-danger"><em>Failed to load messages.</em></div>');
        }
    });
}

@else
$(document).ready(function() {
    console.log('No active session, skipping chat initialization');
    updateUnreadCount();
});
@endif

function insertQuickReply(text) {
    const messageInput = $('input[name="message"]');
    messageInput.val(text);
    messageInput.focus();
}

function endSession() {
    @if(isset($session))
        const sessionId = {{ $session->id_session }};
    @else
        const sessionId = 0;
    @endif

    showConfirm('Are you sure you want to end this session?', function () {
        $.ajax({
            url: adminChatRoutes.endSession(sessionId), // FIXED: Use route helper
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    showToast('Session ended successfully');
                    location.reload();
                } else {
                    showToast('Failed to end session');
                }
            },
            error: function(xhr) {
                console.error('Error ending session:', xhr.responseText);
                let errorMsg = 'Error ending session';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.error) {
                        errorMsg = errorResponse.error;
                    }
                } catch (e) {}
                showToast(errorMsg);
            }
        });
    });
}

function archiveSession(sessionId) {
    showConfirm('Archive this session? It will be moved to your archive and removed from your active list.', function () {
        $.ajax({
            url: adminChatRoutes.archiveSession(sessionId), // FIXED: Use route helper
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message || 'Session archived successfully');
                    window.location.href = "{{ route('admin.konseling.index') }}";
                } else {
                    showToast('Failed to archive session');
                }
            },
            error: function(xhr) {
                console.error('Error archiving session:', xhr.responseText);
                let errorMsg = 'Failed to archive session';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.error) {
                        errorMsg = errorResponse.error;
                    }
                } catch (e) {}
                showToast(errorMsg);
            }
        });
    });
}

function deleteSession(sessionId) {
    @if(isset($session))
        const studentViewStatus = '{{ $session->getViewStatus("student", $session->id_student) }}';

        let confirmMessage = 'Delete this session? ';

        if (studentViewStatus === 'hidden') {
            confirmMessage += 'Since the student has already deleted it, this will PERMANENTLY DELETE the session and all messages from the database. This action cannot be undone.';
        } else {
            confirmMessage += 'It will be removed from your view but the student can still access it. If the student also deletes it later, the session will be permanently removed.';
        }
    @else
        let confirmMessage = 'Delete this session? It will be removed from your view.';
    @endif

    showConfirm(confirmMessage, function () {
        console.log('🗑️ Counselor deleting session:', sessionId);

        $.ajax({
            url: adminChatRoutes.deleteSession(sessionId), // FIXED: Use route helper
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            success: function(response) {
                console.log('✅ Delete response:', response);

                if (response.success) {
                    showToast(response.message);

                    setTimeout(() => {
                        window.location.href = "{{ route('admin.konseling.index') }}";
                    }, 1500);
                } else {
                    showToast('Failed to delete session');
                }
            },
            error: function(xhr) {
                console.error('❌ Delete error:', xhr);

                let errorMsg = 'Failed to delete session';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.error) {
                        errorMsg = errorResponse.error;
                    }
                } catch (e) {
                    if (xhr.status === 422) {
                        errorMsg = 'Session cannot be deleted. Please ensure the session has ended.';
                    } else if (xhr.status === 403) {
                        errorMsg = 'You do not have permission to delete this session.';
                    } else if (xhr.status === 404) {
                        errorMsg = 'Session not found.';
                    }
                }
                showToast(errorMsg);
            }
        });
    });
}

function updateUnreadCount() {
    $.ajax({
        url: adminChatRoutes.stats,
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(stats) {
            console.log('📊 Admin stats updated:', stats);
            
            // Update totals
            $('#total-sessions').text(stats.total_sessions || 0);
            $('#total-unread').text(stats.total_unread || 0);
            
            // ✅ NEW: Update individual session badges
            if (stats.sessions && Array.isArray(stats.sessions)) {
                // First, remove badges from sessions with 0 unread
                const allSessionLinks = $('a[href*="/admin/konseling/"]').filter(function() {
                    return $(this).attr('href').match(/\/admin\/konseling\/\d+$/);
                });
                
                allSessionLinks.each(function() {
                    const $link = $(this);
                    const sessionId = $link.attr('href').match(/\/(\d+)$/)?.[1];
                    
                    if (sessionId) {
                        const sessionStat = stats.sessions.find(s => s.id_session == sessionId);
                        const $badge = $link.find('.unread-badge');
                        
                        if (!sessionStat || sessionStat.unread_count === 0) {
                            // Remove badge if exists
                            if ($badge.length > 0) {
                                $badge.fadeOut(200, function() {
                                    $(this).remove();
                                });
                            }
                        }
                    }
                });
                
                // Update or add badges for sessions with unread messages
                stats.sessions.forEach(function(session) {
                    if (session.unread_count > 0) {
                        const $sessionLink = $(`a[href$="/admin/konseling/${session.id_session}"]`);
                        
                        if ($sessionLink.length > 0) {
                            const $badge = $sessionLink.find('.unread-badge');
                            
                            if ($badge.length > 0) {
                                // Update existing badge
                                if ($badge.text() != session.unread_count) {
                                    $badge.text(session.unread_count);
                                }
                            } else {
                                // Create new badge
                                const badgeHtml = `<span class="unread-badge">${session.unread_count}</span>`;
                                const $container = $sessionLink.find('.d-flex.align-items-center.justify-content-between');
                                
                                if ($container.length > 0) {
                                    $container.append(badgeHtml);
                                } else {
                                    $sessionLink.find('strong').after(badgeHtml);
                                }
                            }
                        }
                    }
                });
            }
        },
        error: function(xhr) {
            console.error('❌ Error getting stats:', xhr.responseText);
        }
    });
}

// Call it on page load
$(document).ready(function() {
    updateUnreadCount();
});

// Call it every 30 seconds
setInterval(updateUnreadCount, 30000);
</script>
@endif
@endpush
