@extends('public.layouts.layout')

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
</style>
@endpush

@section('content')
<div class="chat-container">
    <div class="row h-100 g-0">
        <div class="col-12 d-flex flex-column" style="height: 100%; overflow:hidden;">
            
            <!-- Archived Session Banner -->
            <div class="archived-banner">
                <div class="badge bg-light text-dark mb-2">
                    <i class="fas fa-archive me-1"></i>
                    Arsip Sesi Konseling
                </div>
                <h6 class="mb-1">{{ $session->counselor->counselor_name ?? 'Konselor' }}</h6>
                <small class="opacity-75">
                    Sesi dengan {{ $session->counselor->counselor_name ?? 'Konselor' }} • 
                    Diarsip: {{ $archiveRecord ? \Carbon\Carbon::parse($archiveRecord->archived_at)->format('d M Y, H:i') : 'N/A' }}
                </small>
            </div>

            <!-- Header -->
            <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between flex-shrink-0">
                <div class="d-flex align-items-center">
                    <img src="{{ $session->counselor->photo ? asset('storage/' . $session->counselor->photo) : 'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22><rect width=%22100%25%22 height=%22100%25%22 fill=%22%23ddd%22/><text x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22>👤</text></svg>' }}" 
                         class="rounded-circle me-3" width="40" height="40" style="object-fit:cover;">
                    <div>
                        <h6 class="mb-0">{{ $session->counselor->counselor_name ?? 'Konselor' }}</h6>
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
                    <a href="{{ route('public.konseling.archive-list') }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Arsip
                    </a>
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
                            $messageDate = $message->sent_at->format('Y-m-d');
                            $messageTime = $message->sent_at->format('H:i');
                            $isFromStudent = $message->sender_type === 'student';
                        @endphp
                        
                        <!-- Date divider -->
                        @if($currentDate !== $messageDate)
                            <div class="date-divider">
                                <span>{{ $message->sent_at->format('l, d M Y') }}</span>
                            </div>
                            @php $currentDate = $messageDate; @endphp
                        @endif
                        
                        <!-- Message -->
                        <div class="d-flex {{ $isFromStudent ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                            <div class="chat-bubble {{ $isFromStudent ? 'sent' : 'received' }}">
                                <div>{{ $message->message }}</div>
                                <div class="message-meta">
                                    {{ $messageTime }}
                                    @if($isFromStudent)
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
                        <p>Tidak ada pesan dalam sesi arsip ini</p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="p-3 border-top bg-light flex-shrink-0 text-center">
                <div class="text-muted">
                    <i class="fas fa-info-circle me-2"></i>
                    Ini adalah sesi yang sudah diarsip. Tidak bisa mengirim pesan baru.
                </div>
                @if($archiveRecord && $archiveRecord->session_started_at)
                <div class="mt-2">
                    <small class="text-muted">
                        Periode Sesi: 
                        {{ \Carbon\Carbon::parse($archiveRecord->session_started_at)->format('d M Y') }}
                        @if($archiveRecord->session_ended_at)
                            - {{ \Carbon\Carbon::parse($archiveRecord->session_ended_at)->format('d M Y') }}
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