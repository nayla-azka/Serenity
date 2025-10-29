@extends('admin.layouts.layout')

@section('title', 'View Archived Session')

@push('styles')
<style>
    .chat-container {
        height: calc(100vh - 120px);
        overflow: hidden;
    }

    .chat-bubble {
        border-radius: 1rem;
        max-width: 70%;
        word-break: break-word;
        overflow-wrap: break-word;
        animation: fadeInUp 0.3s ease;
        display: inline-block;
        padding: 0.75rem 1rem;
        margin: 0.25rem 0;
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

    .archived-banner {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: white;
        padding: 1rem;
        text-align: center;
        border-radius: 10px 10px 0 0;
        box-shadow: 0 2px 4px rgba(108, 117, 125, 0.3);
    }

    .archived-banner .badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        margin-bottom: 0.5rem;
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

    .btn-back {
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      position: relative !important;
      width: 100px !important;
      height: 36px !important;
      background: #fff !important;
      border-radius: 12px !important;
      overflow: hidden !important;
      text-decoration: none !important;
    }
    .btn-back__icon {
      position: absolute !important;
      left: 3px !important;
      top: 3px !important;
      width: 30px !important;
      height: 30px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      background: #606970 !important;
      border-radius: 10px !important;
      transition: width 0.4s ease !important;
      z-index: 1 !important;
    }
    .btn-back__text {
      margin-left: 55px !important;
      position: relative !important;
      z-index: 2 !important;
      transition: color 0.4s ease !important;
    }
    .btn-back:hover .btn-back__icon {
      width: 94px !important;
    }
    .btn-back:hover .btn-back__text {
      color: #fff !important;
    }
</style>
@endpush

@section('content')
<div class="section-header text-black mb-4">
    <div class="section-header-back">
        <a href="{{ route('admin.konseling.archive-list') }}" class="btn-back">
            <span class="btn-back__icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" height="20" width="20">
                    <path d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z" fill="#fff"/>
                    <path d="m237.248 512 265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z" fill="#fff"/>
                </svg>
            </span>
        </a>
    </div>
    <h2 class="section-title"> <i class="fas fa-archive me-2" style="color: #6c757d;"></i> Arsip Sesi Konseling</h2>
</div>
<div class="chat-container">
    <div class="row h-100 g-0">
        <div class="col-12 d-flex flex-column" style="height: 100%; overflow:hidden;">

            <!-- Archived Session Banner -->
            <div class="archived-banner">
                <div class="badge bg-light text-dark mb-2">
                    <i class="fas fa-archive me-1"></i>
                    Arsip Sesi Konseling
                </div>
                <h6 class="mb-1">{{ $archivedSession->student_name ?? $archivedSession->user_name ?? 'Unknown Student' }}</h6>
                <small class="opacity-75">
                    @if(isset($archivedSession->student) && $archivedSession->student->class)
                        Kelas: {{ $archivedSession->student->class->class_name }} •
                    @endif
                    Diarsip: {{ \Carbon\Carbon::parse($archivedSession->archived_at)->format('d M Y, H:i') }}
                </small>
            </div>

            <!-- Header -->
            <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between flex-shrink-0">
                <div class="d-flex align-items-center">
                    <img src="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22><rect width=%22100%25%22 height=%22100%25%22 fill=%22%23ddd%22/><text x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22>👤</text></svg>"
                         class="rounded-circle me-3" width="40" height="40" style="object-fit:cover;">
                    <div>
                        <h6 class="mb-0">{{ $archivedSession->student_name ?? $archivedSession->user_name ?? 'Unknown Student' }}</h6>
                        <small class="text-muted">
                            Arsip Sesi Konseling • {{ $messages->count() }} pesan
                        </small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-secondary fs-6">
                        <i class="fas fa-archive me-1"></i>
                        Archived
                    </span>
                </div>
            </div>

            <!-- Chat Messages -->
            <div id="chat-box" class="flex-grow-1 p-3 overflow-auto" style="background:#fafafa;">
                @if($messages->count() > 0)
                    @php
                        $currentDate = null;
                    @endphp

                    @foreach($messages as $message)
                        @php
                            $messageDate = \Carbon\Carbon::parse($message->sent_at)->format('Y-m-d');
                            $messageTime = \Carbon\Carbon::parse($message->sent_at)->format('H:i');
                            $isFromCounselor = $message->sender_type === 'counselor';
                        @endphp

                        <!-- Date divider -->
                        @if($currentDate !== $messageDate)
                            <div class="date-divider">
                                <span>{{ \Carbon\Carbon::parse($message->sent_at)->format('l, d M Y') }}</span>
                            </div>
                            @php $currentDate = $messageDate; @endphp
                        @endif

                        <!-- Message -->
                        <div class="d-flex {{ $isFromCounselor ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                            <div class="chat-bubble {{ $isFromCounselor ? 'sent' : 'received' }}">
                                <div>{{ $message->message }}</div>
                                <div class="message-meta">
                                    {{ $messageTime }}
                                    @if($isFromCounselor)
                                        @if($message->status === 'read')
                                            <i class="fas fa-check-double ms-1"></i>
                                        @elseif($message->status === 'sent')
                                            <i class="fas fa-check ms-1"></i>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-comments fa-2x mb-2"></i>
                        <p>No messages found in this archived session</p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="p-3 border-top bg-light flex-shrink-0 text-center">
                <div class="text-muted">
                    <i class="fas fa-info-circle me-2"></i>
                    This is an archived session. No new messages can be sent.
                </div>
                @if($archivedSession->session_started_at)
                <div class="mt-2">
                    <small class="text-muted">
                        Session Period:
                        {{ \Carbon\Carbon::parse($archivedSession->session_started_at)->format('d M Y') }}
                        @if($archivedSession->session_ended_at)
                            - {{ \Carbon\Carbon::parse($archivedSession->session_ended_at)->format('d M Y') }}
                        @endif
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>
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