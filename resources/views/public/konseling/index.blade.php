@extends('public.layouts.layout')

@push('styles')
<style>
    html, body {
        height: 100vh;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* WhatsApp-like responsive behavior */
    .chat-container {
        background: #fafafa;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Desktop: show both */
    @media (min-width: 768px) {
        .chat-sidebar {
            display: block !important;
        }
        .chat-main {
            display: flex !important;
        }
    }

    /* Mobile: toggle between sidebar and chat */
    @media (max-width: 767.98px) {
        .chat-container {
            border-radius: 0 !important;
        }

        /* Default: show sidebar, hide chat */
        .chat-sidebar {
            display: block;
            width: 100%;
            max-width: 100%;
        }

        .chat-main {
            display: none;
            width: 100%;
            max-width: 100%;
        }

        /* When chat is active: hide sidebar, show chat */
        .chat-container.chat-active .chat-sidebar {
            display: none;
        }

        .chat-container.chat-active .chat-main {
            display: flex;
        }

        /* Back button for mobile */
        .mobile-back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: transparent;
            border: none;
            color: white;
            cursor: pointer;
            margin-right: 8px;
            transition: background 0.2s;
        }

        .mobile-back-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .mobile-back-btn i {
            font-size: 1.2rem;
        }
    }

    /* Hide back button on desktop */
    @media (min-width: 768px) {
        .mobile-back-btn {
            display: none !important;
        }
    }

    /* Enhanced Chat System Styles */
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
        background: #dcd9ee;
        color: #151515;
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

    #send-btn {
        min-width: 60px;
        transition: all 0.3s ease;
        background-color: #837ab6;
    }

    #send-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }

    .d-flex {
        display: flex !important;
    }

    .justify-content-end {
        justify-content: flex-end !important;
    }

    .justify-content-start {
        justify-content: flex-start !important;
    }

    .mb-2 {
        margin-bottom: 0.5rem !important;
    }

    .ms-1 {
        margin-left: 0.25rem !important;
    }

    .text-center {
        text-align: center !important;
    }

    .text-muted {
        color: #6c757d !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    #success-message, #error-message {
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }

    #success-message {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    #error-message {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .d-none {
        display: none !important;
    }

    .fas {
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
    }

    input[name="message"]:focus {
        border-color: #837ab6;
        box-shadow: 0 0 0 0.2rem rgba(131, 122, 182, 0.25);
    }

    #send-btn:focus {
        box-shadow: 0 0 0 0.2rem rgba(131, 122, 182, 0.25);
    }

    .chat-main {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    #chat-box {
        scroll-behavior: smooth;
        flex-grow: 1;
        overflow-y: auto;
        min-height: 0;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .spinner-border {
        display: inline-block;
        width: 2rem;
        height: 2rem;
        vertical-align: text-bottom;
        border: 0.25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spin 0.75s linear infinite;
    }

    .row.g-0, .col-3, .col-9 {
        margin: 0;
        padding: 0;
    }

    footer {
        display: none !important;
    }

    .session-ended-banner {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        padding: 1rem;
        text-align: center;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
        animation: slideDown 0.5s ease-out;
    }

    .session-ended-banner .badge {
        background: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        margin-bottom: 0.5rem;
        border-radius: 15px;
    }

    .session-ended-banner p {
        margin: 0;
        font-size: 0.9rem;
        opacity: 0.95;
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

    .new-session-area {
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

    .new-session-btn {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        color: white;
        padding: 0.8rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    }

    .new-session-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
        color: white;
    }

    .text-warning { 
        color: #ffc107 !important; 
    }

    .dropdown-menu {
        border: 1px solid rgba(0,0,0,0.15);
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        z-index: 1050 !important;
        position: absolute !important;
    }

    .dropdown-item {
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        color: #212529;
        text-decoration: none;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #16181b;
    }

    .dropdown-item.text-danger {
        color: #dc3545 !important;
    }

    .dropdown-item.text-danger:hover {
        background-color: #f5c6cb;
        color: #721c24 !important;
    }

    .dropdown {
        position: relative;
    }

    .dropdown-toggle::after {
        display: inline-block;
        margin-left: 0.255em;
        vertical-align: 0.255em;
        content: "";
        border-top: 0.3em solid;
        border-right: 0.3em solid transparent;
        border-bottom: 0;
        border-left: 0.3em solid transparent;
    }

    .bg-chat {
        color: rgb(248, 246, 255) !important;
        background-color: #663c71 !important;
    }

    .bg-chat2 {
        background: linear-gradient(rgb(235, 232, 255), rgb(252, 251, 255)) !important;
    }

    .bg-chat3 {
        background: rgb(131, 122, 182) !important;
    }

    .quick-reply-btn {
        transition: all 0.2s ease;
    }

    .quick-reply-btn:hover {
        background-color: #837ab6 !important;
        color: white !important;
        transform: scale(1.05);
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

    .counselor-status-dot {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 13px;
        height: 13px;
        background: #28a745;
        border: 1px solid #fff;
        border-radius: 50%;
        z-index: 2;
    }

    .unread-badge {
        background: linear-gradient(135deg, #dc3545, #e74c3c);
        color: white;
        border-radius: 50% !important;
        width: 22px !important;
        height: 22px !important;
        aspect-ratio: 1 / 1 !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: bold;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
        animation: pulse 2s infinite;
        line-height: 1;
        flex-shrink: 0;
    }

    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
        }
        50% {
            box-shadow: 0 2px 16px rgba(220, 53, 69, 0.7);
        }
    }

    /* ========== MODAL STYLES ========== */
    .modal-dialog {
        margin: 1rem;
    }

    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 500px;
            margin: 1.75rem auto;
        }
    }

    @media (max-width: 575.98px) {
        .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }
        
        .modal-dialog-centered {
            min-height: calc(100% - 1rem);
        }
    }

    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border: none;
    }

    .modal-header .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        display: flex;
        align-items: center;
    }

    .modal-header .modal-title i {
        color: white !important;
        font-size: 1.4rem;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
        transition: opacity 0.2s;
    }

    .modal-header .btn-close:hover {
        opacity: 1;
    }

    .modal-body {
        padding: 1.5rem;
        background: #ffffff;
    }

    @media (max-width: 575.98px) {
        .modal-header {
            padding: 1.25rem 1rem;
        }
        
        .modal-header .modal-title {
            font-size: 1.1rem;
        }
        
        .modal-header .modal-title i {
            font-size: 1.2rem;
        }
        
        .modal-body {
            padding: 1rem;
        }
    }

    .modal-body > p.text-muted {
        font-size: 0.95rem;
        margin-bottom: 1.25rem;
        text-align: center;
    }

    /* Session Option Cards */
    .session-option-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        background: white;
    }

    @media (max-width: 575.98px) {
        .session-option-card {
            padding: 1rem;
            border-radius: 10px;
        }
    }

    .session-option-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s;
    }

    .session-option-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .session-option-card:hover::before {
        left: 100%;
    }

    .session-option-card:active {
        transform: translateY(-1px);
    }

    /* Warning/Archive Option */
    .session-option-card[data-option="keep"] {
        border-color: #ffc107;
    }

    .session-option-card[data-option="keep"]:hover {
        border-color: #ffb300;
        background: linear-gradient(135deg, #fff9e6 0%, #fffbf0 100%);
    }

    .session-option-card[data-option="keep"].selected {
        border-color: #ffc107;
        background: linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%);
        box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2);
    }

    /* Danger/Delete Option */
    .session-option-card[data-option="delete"] {
        border-color: #dc3545;
        margin-bottom: 0rem;
    }

    .session-option-card[data-option="delete"]:hover {
        border-color: #c82333;
        background: linear-gradient(135deg, #ffe6e9 0%, #fff0f2 100%);
    }

    .session-option-card[data-option="delete"].selected {
        border-color: #dc3545;
        background: linear-gradient(135deg, #f8d7da 0%, #fce4e6 100%);
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.2);
    }

    /* Option Icons */
    .option-icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        display: block;
        transition: transform 0.3s ease;
    }

    @media (max-width: 575.98px) {
        .option-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
    }

    .session-option-card:hover .option-icon {
        transform: scale(1.1);
    }

    .session-option-card[data-option="keep"] .option-icon {
        color: #ffc107;
    }

    .session-option-card[data-option="delete"] .option-icon {
        color: #dc3545;
    }

    /* Text inside cards */
    .session-option-card h6 {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #212529;
    }

    @media (max-width: 575.98px) {
        .session-option-card h6 {
            font-size: 1rem;
        }
    }

    .session-option-card p {
        font-size: 0.9rem;
        line-height: 1.5;
        color: #6c757d;
        margin-bottom: 0;
    }

    @media (max-width: 575.98px) {
        .session-option-card p {
            font-size: 0.85rem;
            line-height: 1.4;
        }
    }

    /* Modal Footer */
    .modal-footer {
        padding: 1.25rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        gap: 0.5rem;
    }

    @media (max-width: 575.98px) {
        .modal-footer {
            padding: 1rem;
            flex-direction: column-reverse;
        }
        
        .modal-footer .btn {
            width: 100%;
            margin: 0;
        }
    }

    .modal-footer .btn {
        padding: 0.625rem 1.5rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        border: none;
    }

    @media (max-width: 575.98px) {
        .modal-footer .btn {
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
        }
    }

    .modal-footer .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .modal-footer .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
    }

    .modal-footer .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .modal-footer .btn-primary:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .modal-footer .btn-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .modal-footer .btn i {
        font-size: 0.9rem;
    }

    /* Selection indicator for cards */
    .session-option-card.selected::after {
        content: '\f00c';
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        top: 12px;
        right: 12px;
        width: 28px;
        height: 28px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        animation: checkmarkPop 0.3s ease;
    }

    .session-option-card[data-option="keep"].selected::after {
        color: #ffc107;
    }

    .session-option-card[data-option="delete"].selected::after {
        color: #dc3545;
    }

    @keyframes checkmarkPop {
        0% {
            transform: scale(0);
            opacity: 0;
        }
        50% {
            transform: scale(1.2);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Modal backdrop */
    .modal-backdrop.show {
        opacity: 0.6;
    }

    /* Smooth modal animation */
    .modal.fade .modal-dialog {
        transform: scale(0.9) translateY(-50px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modal.show .modal-dialog {
        transform: scale(1) translateY(0);
        opacity: 1;
    }

    /* Loading state for button */
    .modal-footer .btn-primary:disabled .spinner-border {
        width: 1rem;
        height: 1rem;
        border-width: 2px;
    }

    /* ========== MOBILE RESPONSIVE ========== */
    @media (max-width: 767.98px) {
        .chat-container.chat-active ~ nav,
        nav:has(+ .chat-container.chat-active) {
            display: none !important;
        }
        
        .session-item .p-3 {
            padding: 0.75rem !important;
        }

        .session-item img.rounded-circle {
            width: 40px !important;
            height: 40px !important;
        }

        .session-item strong {
            font-size: 0.95rem;
        }

        .session-preview {
            font-size: 0.85rem;
        }

        .session-item small {
            font-size: 0.75rem;
        }

        .p-2.bg-light.border-bottom {
            padding: 0.5rem !important;
        }

        .unread-badge {
            width: 18px;
            height: 18px;
            font-size: 11px;
        }

        .chat-bubble {
            max-width: 85%;
            padding: 0.5rem 0.75rem;
        }

        .chat-main {
            display: none !important;
        }

        .chat-container.chat-active .chat-main {
            display: flex !important;
            flex-direction: column;
            height: 100vh;
        }

        .chat-container.chat-active .chat-sidebar {
            display: none !important;
        }

        #chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            background-color: #f9f9f9;
        }

        .p-3.border-top.bg-white.flex-shrink-0 {
            position: sticky;
            bottom: 0;
            background-color: #fff;
            border-top: 1px solid #ddd;
            padding: 10px;
        }

        .chat-container {
            height: 100vh !important;
            overflow: hidden;
        }

        .chat-sidebar {
            height: 100vh;
            overflow-y: auto;
        }
    }

</style>


@endpush

@section('content')

{{-- @php
    $isChatActive = request()->has('session'); // misal ?session=3
@endphp --}}

<div class="chat-container bg-chat2 {{ isset($session) ? 'chat-active' : '' }}" style="border-radius: 0% !important;">
    <div class="row h-100 g-0">
        <!-- Sidebar - Counselor List -->
        <div class="col-md-3 col-12 rounded-0 border-end chat-sidebar">
            <!-- Header -->
            <div class="p-3 rounded-0 bg-chat3 text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">
                            <i class="fas fa-comments me-2"></i>Konselor
                        </h6>
                        <small class="opacity-75">{{ Auth::user()->name ?? 'Siswa' }}</small>
                    </div>
                    <button class="dt dt-btn create" type="button">
                        <a class="dropdown-item" href="{{ route('public.konseling.archive-list') }}">
                            <i class="bi bi-archive"></i>
                        </a>
                    </button>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="p-2 bg-chat3 border-bottom">
                <div class="row text-center g-2">
                    <div class="col-6">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-2">
                                <small class="text-muted d-block">Total</small>
                                <strong id="total-sessions">{{ $allSessions->count() }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-2">
                                <small class="text-muted d-block">Pesan Baru</small>
                                <strong id="total-unread" class="text-success">
                                    {{ $allSessions->sum('unread_count') ?? 0 }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Counselors List -->
            @if($counselors->count() > 0)
                @foreach($counselors as $c)
                    @php
                        $activeSession = $allSessions->firstWhere('id_counselor', $c->id_counselor);
                        $isSelected = isset($session) && $session->id_counselor == $c->id_counselor;
                        $latestMsg = $activeSession ? $activeSession->latestMessage : null;
                        $preview = $latestMsg ? \Str::limit($latestMsg->message, 40) : 'Mulai percakapan';
                        $timeAgo = $latestMsg ? $latestMsg->sent_at->diffForHumans() : ($activeSession ? $activeSession->created_at->diffForHumans() : '');
                        $unreadCount = $activeSession ? ($activeSession->unread_count ?? 0) : 0;
                    @endphp

                    <a href="{{ $activeSession ? route('public.konseling.show', $activeSession->id_session) : route('public.konseling.start', $c->id_counselor) }}"
                       class="d-block text-decoration-none text-dark session-item {{ $isSelected ? 'active' : '' }}"
                       style="border-bottom: 1px solid #eee;">
                        <div class="p-3 position-relative">
                            <div class="d-flex align-items-start">
                                <div class="position-relative me-3">
                                    <img src="{{ asset('storage/' . $c->photo) }}"
                                         class="rounded-circle" width="45" height="45" style="object-fit:cover;"
                                         onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2245%22 height=%2245%22><rect width=%22100%25%22 height=%22100%25%22 fill=%22%23ddd%22/><text x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22>👤</text></svg>'">
                                    @if($activeSession && $activeSession->is_active)
                                        <span class="counselor-status-dot"></span>
                                    @endif
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="d-flex align-items-center justify-content-between position-relative">
                                        <strong class="text-truncate">{{ $c->counselor_name }}</strong>
                                        @if($unreadCount > 0)
                                            <span class="unread-badge">{{ $unreadCount }}</span>
                                        @endif
                                    </div>
                                    <small class="text-muted d-block">{{ $c->kelas }}</small>
                                    <div class="session-preview text-muted small">{{ $preview }}</div>
                                    @if($timeAgo)
                                        <small class="text-muted">{{ $timeAgo }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            @else
                <div class="text-center text-muted p-4">
                    <i class="fas fa-user-friends fa-2x mb-2"></i>
                    <p>Belum ada konselor tersedia</p>
                </div>
            @endif
        </div>

        <!-- Chat Area -->
        <div class="col-md-9 col-12 chat-main p-2">
            @if(isset($session))
                @php
                    $counselorName = $session->counselor->counselor_name ?? 'Unknown Counselor';
                    $counselorClass = $session->counselor->kelas ?? 'Unknown Class';
                    $isPreviewMode = isset($session->preview_mode) && $session->preview_mode;
                @endphp

                <!-- Chat Header -->
                <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between flex-shrink-0">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-link text-dark d-md-none me-2" onclick="goBackToSidebar()" style="font-size: 1.2rem;">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <img src="{{ asset('storage/' . $session->counselor->photo) }}"
                            class="rounded-circle me-3" width="40" height="40" style="object-fit:cover;"
                            onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22><rect width=%22100%25%22 height=%22100%25%22 fill=%22%23ddd%22/><text x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22>👤</text></svg>'">
                        <div>
                            <h6 class="mb-0">{{ $counselorName }}</h6>
                            <small class="text-muted">{{ $counselorClass }} • Topik: {{ $session->topic }}</small>
                        </div>
                    </div>

                    <div class="d-flex gap-2 chat-header-right">
                        @if($isPreviewMode)
                            <span class="badge bg-info">
                                <i class="fas fa-eye"></i> Mode Preview
                            </span>
                        @elseif(isset($session->is_active) && $session->is_active)
                            <span class="badge bg-success">
                                <i class="fas fa-circle"></i> Aktif
                            </span>
                        @else
                             <span class="badge bg-secondary fs-10">
                                <i class="fas fa-stop-circle me-1"></i>
                                Sesi Selesai
                            </span>
                        @endif
                    </div>
                </div>

                @if($isPreviewMode)
                    <!-- Preview Mode (same as before) -->
                    <div id="chat-box" class="flex-grow-1">
                        <div class="preview-chat-area p-2">
                            <div class="preview-content">
                                <h5>
                                    <i class="fas fa-comment-dots preview-icon"></i>
                                    Kirim pesan untuk memulai sesi konseling mu dengan {{ $counselorName }}
                                </h5>

                                @if($session->welcome_message)
                                    <div class="d-flex justify-content-start mb-3">
                                        <div class="chat-bubble received preview">
                                            <div>{{ $session->welcome_message }}</div>
                                            <span class="preview-badge"></span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-3 border-top bg-white flex-shrink-0">
                        <form id="chat-form" class="d-flex mb-2">
                            @csrf
                            <input type="hidden" name="id_counselor" value="{{ $session->id_counselor }}">
                            <input type="text"
                                   name="message"
                                   class="form-control me-2"
                                   placeholder="Type your first message to start the session..."
                                   required
                                   maxlength="1000"
                                   autocomplete="off">
                            <button type="submit" class="dt dt-btn create send" id="send-btn" style="min-width: 80px;">
                                <span id="send-icon"><i class="fas fa-rocket"></i> Start</span>
                                <span id="send-loading" style="display: none;">
                                    <div class="spinner-border spinner-border-sm"></div>
                                </span>
                            </button>
                        </form>

                        <div class="d-flex flex-wrap gap-1">
                            <small class="text-muted me-2 align-self-center">Quick Start:</small>
                            <button class="btn btn-sm btn-outline-secondary quick-reply-btn" onclick="insertQuickMessage('Halo, saya ingin berkonsultasi dengan Anda.')">
                                <i class="fas fa-hand-wave"></i> Salam
                            </button>
                            <button class="btn btn-sm btn-outline-secondary quick-reply-btn" onclick="insertQuickMessage('Saya butuh bantuan untuk membahas sesuatu.')">
                                <i class="fas fa-question-circle"></i> Minta Bantuan
                            </button>
                        </div>
                    </div>

                @elseif(isset($session->is_active) && !$session->is_active)
                    <!-- Ended Session (same as before) -->
                    <div class="session-ended-banner">
                        <div class="badge bg-light text-dark mb-2">
                            <i class="fas fa-info-circle me-1"></i>
                            Sesi Diakhiri
                        </div>
                        <p class="mb-0 small">Sesi konseling ini telah diakhiri oleh Konselor.</p>
                    </div>

                    <div id="chat-box" class="flex-grow-1">
                        <div id="loading" class="text-center text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            Memuat Pesan...
                        </div>
                    </div>

                    <div class="new-session-area flex-shrink-0">
                        <div class="mb-3">
                            <i class="fas fa-comments-slash fa-2x text-muted mb-3"></i>
                            <h5 class="text-muted mb-2">Sesi Konseling Berakhir</h5>
                            <p class="text-muted small mb-4">Sesi konseling mu dengan {{ $counselorName }} telah berakhir.</p>
                        </div>
                        <button class="btn new-session-btn" onclick="showNewSessionModal()">
                            <i class="fas fa-plus-circle me-2"></i>
                            Mulai Sesi Baru
                        </button>
                    </div>

                @else
                    <!-- Active Session (same as before) -->
                    <div id="chat-box" class="flex-grow-1">
                        <div id="loading" class="text-center text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            Memuat Pesan...
                        </div>
                    </div>

                    <div class="p-3 border-top bg-white flex-shrink-0">
                        <form id="chat-form" class="d-flex mb-2">
                            @csrf
                            <input type="hidden" name="id_session" value="{{ $session->id_session ?? '' }}">
                            <input type="text"
                                   name="message"
                                   class="form-control me-2"
                                   placeholder="Ketik pesan..."
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

                        <!-- Quick Messages -->
                        <div class="d-flex flex-wrap gap-1">
                            <small class="text-muted me-2 align-self-center">Pesan Cepat:</small>
                            <button class="btn btn-sm btn-outline-secondary quick-reply-btn" onclick="insertQuickMessage('Halo, saya ingin berkonsultasi.')">
                                <i class="fas fa-hand-wave"></i> Salam
                            </button>
                            <button class="btn btn-sm btn-outline-secondary quick-reply-btn" onclick="insertQuickMessage('Terima kasih atas bantuannya.')">
                                <i class="fas fa-heart"></i> Terima Kasih
                            </button>
                        </div>
                    </div>
                @endif

            @else
                <!-- Empty State -->
                <div class="d-none d-lg-flex flex-column align-items-center justify-content-center h-100">
                    <div class="text-center text-muted">
                        <div class="mb-3">
                            <i class="fas fa-comments fa-3x text-primary"></i>
                        </div>
                        <h5>Selamat datang di Ruang Konseling Digital</h5>
                        <p>Pilih konselor dari sidebar untuk memulai percakapan</p>
                        <div class="mt-4">
                            <div class="row justify-content-center g-3">
                                <div class="col-auto">
                                    <div class="card border-0" style="background-color: rgba(0, 123, 255, 0.1);">
                                        <div class="card-body text-center">
                                            <i class="fas fa-user-friends mb-2 text-primary"></i>
                                            <h6>Konselor Tersedia</h6>
                                            <h4>{{ $counselors->count() }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="card border-0" style="background-color: rgba(0, 123, 255, 0.1);">
                                        <div class="card-body text-center">
                                            <i class="fas fa-comments mb-2 text-success"></i>
                                            <h6>Chat Aktif</h6>
                                            <h4>{{ $allSessions->where('is_active', true)->count() }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="card border-0" style="background-color: rgba(0, 123, 255, 0.1);">
                                        <div class="card-body text-center">
                                            <i class="fas fa-history mb-2 text-info"></i>
                                            <h6>Total Percakapan</h6>
                                            <h4>{{ $allSessions->count() }}</h4>
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

<!-- New Session Modal -->
<div class="modal fade" id="newSessionModal" tabindex="-1" aria-labelledby="newSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newSessionModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>
                    Mulai Sesi Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Apa yang akan anda lakukan dengan sesi ini?</p>
                
                <div class="session-option-card" data-option="keep" role="button" tabindex="0">
                    <div class="text-center">
                        <i class="fas fa-archive option-icon"></i>
                        <h6 class="fw-bold">Arsip Sesi</h6>
                        <p class="small mb-0">
                            Arsipkan sesi ini dan mulai sesi konseling baru.
                            Anda bisa melihat sesi yang diarsip di halaman arsip kapan saja.
                        </p>
                    </div>
                </div>
                
                <div class="session-option-card" data-option="delete" role="button" tabindex="0">
                    <div class="text-center">
                        <i class="fas fa-trash-alt option-icon"></i>
                        <h6 class="fw-bold">Hapus Sesi</h6>
                        <p class="small mb-0">
                            Hapus sesi ini secara permanen dan mulai sesi baru.
                            Konselor tetap memiliki akses untuk membuka sesi ini.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Batal
                </button>
                <button type="button" class="btn btn-primary" onclick="createNewSession()" disabled id="confirmBtn">
                    <i class="fas fa-check me-1"></i>
                    Konfirmasi
                </button>
            </div>
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

// FIXED: Use Laravel route helpers to generate proper URLs
const chatRoutes = {
    send: "{{ route('public.konseling.send') }}",
    fetch: function(sessionId) {
        return "{{ url('serenity/konseling/fetch') }}/" + sessionId;
    },
    markRead: function(sessionId) {
        return "{{ url('serenity/konseling/mark-read') }}/" + sessionId;
    },
    newSession: "{{ route('public.konseling.new-session') }}",
    deleteSession: function(sessionId) {
        return "{{ url('serenity/konseling/delete') }}/" + sessionId;
    },
    stats: "{{ route('public.konseling.stats') }}" // ✅ ADD THIS
};

function updateUnreadCount() {
    $.ajax({
        url: chatRoutes.stats,
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(stats) {
            console.log('📊 Stats updated:', stats);
            
            // Update totals in sidebar
            $('#total-sessions').text(stats.total_sessions || 0);
            $('#total-unread').text(stats.total_unread || 0);
            
            // ✅ Update individual session badges
            if (stats.sessions && Array.isArray(stats.sessions)) {
                stats.sessions.forEach(function(session) {
                    // Find the session link by session ID
                    const sessionLink = $('a[href*="session/' + session.id_session + '"]');
                    const badge = sessionLink.find('.unread-badge');
                    
                    if (session.unread_count > 0) {
                        // Show or update badge
                        if (badge.length > 0) {
                            badge.text(session.unread_count);
                        } else {
                            // Create badge if it doesn't exist
                            const badgeHtml = '<span class="unread-badge">' + session.unread_count + '</span>';
                            sessionLink.find('strong').after(badgeHtml);
                        }
                    } else {
                        // Remove badge if count is 0
                        badge.remove();
                    }
                });
            }
        },
        error: function(xhr) {
            console.error('Error getting stats:', xhr.responseText);
        }
    });
}

// Store CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;


let globalChatHelper = null;

$(document).ready(function() {
    console.log('Student chat system initializing...');

    @if(isset($session->preview_mode) && $session->preview_mode)
        setupPreviewMode();
    @elseif(isset($session) && $session->id_session)
        @if($session->is_active)
            globalChatHelper = new ChatHelper();

            globalChatHelper.initializeChat({
                pusherKey: '{{ config("broadcasting.connections.pusher.key") }}',
                pusherCluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
                sessionId: {{ $session->id_session }},
                currentUserType: 'student',
                formSelector: '#chat-form',
                sendUrl: chatRoutes.send, // FIXED: Use route helper
                fetchUrl: chatRoutes.fetch({{ $session->id_session }}), // FIXED: Use route helper
                csrfToken: csrfToken,
                markAsReadUrl: chatRoutes.markRead({{ $session->id_session }}) // FIXED: Use route helper
            });
        @else
            loadEndedSessionMessages({{ $session->id_session }});
        @endif
    @endif

      // Update stats every 30 seconds
    setInterval(updateUnreadCount, 30000);
    
    // Initial call
    updateUnreadCount();

    console.log('Student chat system initialized successfully');
});

function setupPreviewMode() {
    console.log('Setting up preview mode...');
    let isProcessing = false;
    let hasCreatedSession = false;
    let activeSessionId = null;
    let chatHelperInstance = null;

    $('#chat-form').off('submit').on('submit', function(e) {
        e.preventDefault();

        if (isProcessing) {
            console.log('⚠️ Already processing, ignoring duplicate submission');
            return;
        }

        const messageInput = $('input[name="message"]');
        const message = messageInput.val().trim();

        if (!message) {
            showToast('Please enter a message');
            return;
        }

        // If session already created, send via existing session
        if (hasCreatedSession && activeSessionId) {
            console.log('✅ Session exists, sending message to:', activeSessionId);

            isProcessing = true;
            $('#send-btn').prop('disabled', true);
            $('#send-icon').hide();
            $('#send-loading').show();

            const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

            $.ajax({
                url: chatRoutes.send, // FIXED: Use route helper
                method: 'POST',
                data: {
                    _token: csrfToken,
                    id_session: activeSessionId,
                    message: message,
                    timezone: userTimezone
                },
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    console.log('✅ Message sent:', response);

                    messageInput.val('');

                    let messageToDisplay = response;
                    if (response.messages && Array.isArray(response.messages)) {
                        messageToDisplay = response.messages[0];
                    }

                    if (chatHelperInstance && messageToDisplay) {
                        chatHelperInstance.appendMessage(messageToDisplay, 'student');
                        chatHelperInstance.scrollToBottom();
                    }
                },
                error: function(xhr) {
                    console.error('❌ Error sending message:', xhr);
                    let errorMessage = 'Failed to send message';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.error || errorMessage;
                    } catch (e) {}
                    showToast(errorMessage);
                },
                complete: function() {
                    isProcessing = false;
                    $('#send-btn').prop('disabled', false);
                    $('#send-loading').hide();
                    $('#send-icon').show();
                }
            });
            return;
        }

        // FIRST MESSAGE - create session
        const counselorId = {{ $session->id_counselor ?? 'null' }};
        if (!counselorId) {
            showToast('Invalid counselor selection');
            return;
        }

        isProcessing = true;
        $('#send-btn').prop('disabled', true);
        $('#send-icon').hide();
        $('#send-loading').show();

        const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        console.log('🚀 Creating NEW session with first message...');

        $.ajax({
            url: chatRoutes.send, // FIXED: Use route helper
            method: 'POST',
            data: {
                _token: csrfToken,
                id_counselor: counselorId,
                message: message,
                timezone: userTimezone
            },
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('✅ First message sent:', response);

                if (response.success && response.session_id) {
                    hasCreatedSession = true;
                    activeSessionId = response.session_id;
                    messageInput.val('');
                    $('#chat-box').html('');

                    chatHelperInstance = new ChatHelper();
                    chatHelperInstance.messageCache.clear();
                    chatHelperInstance.lastDate = null;

                    if (response.messages && Array.isArray(response.messages)) {
                        response.messages.forEach((msg) => {
                            chatHelperInstance.appendMessage(msg, 'student');
                        });
                        setTimeout(() => chatHelperInstance.scrollToBottom(), 100);
                    }

                    if (response.is_new_session) {
                        // FIXED: Use proper route generation
                        const newUrl = "{{ url('serenity/konseling/session') }}/" + response.session_id;
                        window.history.replaceState({}, '', newUrl);

                        $('#chat-form').find('input[name="id_counselor"]').remove();
                        $('#chat-form').prepend('<input type="hidden" name="id_session" value="' + response.session_id + '">');
                        $('.badge.bg-info').removeClass('bg-info').addClass('bg-success').html('<i class="fas fa-circle"></i> Aktif');
                        $('#send-icon').html('<i class="fas fa-paper-plane"></i>');
                        $('input[name="message"]').attr('placeholder', 'Ketik pesan...');

                        setTimeout(function() {
                            initializeRealTimeChat(response.session_id, chatHelperInstance);
                        }, 500);
                    }
                }
            },
            error: function(xhr) {
                console.error('❌ Error:', xhr);
                showToast('Failed to send message');
            },
            complete: function() {
                isProcessing = false;
                $('#send-btn').prop('disabled', false);
                $('#send-loading').hide();
                $('#send-icon').show();
            }
        });
    });
}

function initializeRealTimeChat(sessionId, chatHelperInstance) {
    console.log('🔌 Initializing Pusher for session:', sessionId);

    if (!chatHelperInstance) {
        console.error('❌ No ChatHelper instance');
        return;
    }

    const channel = chatHelperInstance.initializePusher(
        '{{ config("broadcasting.connections.pusher.key") }}',
        '{{ config("broadcasting.connections.pusher.options.cluster") }}',
        sessionId
    );

    chatHelperInstance.currentUserType = 'student';
    chatHelperInstance.markAsReadUrl = chatRoutes.markRead(sessionId); // FIXED: Use route helper
    chatHelperInstance.sessionId = sessionId;

    chatHelperInstance.setupConnectionHandlers();
    chatHelperInstance.setupVisibilityTracking();

    if (channel) {
        channel.bind('message.sent', (data) => {
            console.log('📨 New message via Pusher:', data);

            if (data.sender_type !== 'student') {
                chatHelperInstance.appendMessage(data, 'student');
                 // Update badge counts when counselor sends message
                updateUnreadCount();

                // Append message to chat
                if (chatHelperInstance) {
                    chatHelperInstance.appendMessage(data, 'student');
                    
                    if (chatHelperInstance.isPageVisible && chatHelperInstance.isScrolledToBottom()) {
                        setTimeout(() => chatHelperInstance.markMessagesAsRead(), 500);
                    }
                }
            }
        });

        channel.bind('messages.read', (data) => {
            console.log('📬 Read receipt:', data);
            if (data.message_ids && Array.isArray(data.message_ids)) {
                chatHelperInstance.updateMessageReadStatus(data.message_ids);
            }
        });
    }
}

function loadEndedSessionMessages(sessionId) {
    $.ajax({
        url: chatRoutes.fetch(sessionId), // FIXED: Use route helper
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        success: function(messages) {
            $('#loading').hide();
            if (Array.isArray(messages) && messages.length > 0) {
                const chatHelper = new ChatHelper();
                chatHelper.messageCache = new Set();
                messages.forEach(msg => chatHelper.appendMessage(msg, 'student'));
            } else {
                $('#chat-box').append('<div class="text-center text-muted"><em>No messages in this session.</em></div>');
            }
        },
        error: function() {
            $('#loading').hide();
            $('#chat-box').append('<div class="text-center text-danger"><em>Failed to load messages.</em></div>');
        }
    });
}

function createNewSession() {
    if (!selectedOption) {
        showToast('Please select an option');
        return;
    }

    const counselorId = {{ $session->id_counselor ?? 'null' }};
    const sessionId = {{ $session->id_session ?? 'null' }};

    if (!counselorId || !sessionId) {
        showToast('Invalid session data');
        return;
    }

    console.log('🔄 Creating new session...', {
        counselor_id: counselorId,
        session_id: sessionId,
        action: selectedOption
    });

    $('#confirmBtn').prop('disabled', true).html('<div class="spinner-border spinner-border-sm me-2"></div>Processing...');

    $.ajax({
        url: chatRoutes.newSession, // FIXED: Use route helper
        method: 'POST',
        data: {
            _token: csrfToken,
            counselor_id: counselorId,
            action: selectedOption,
            old_session_id: sessionId,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
        },
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            console.log('✅ New session response:', response);

            if (response.success) {
                $('#newSessionModal').modal('hide');

                const action = selectedOption === 'keep' ? 'archived' : 'deleted';
                showToast(`Session ${action} successfully. Starting fresh...`);

                console.log('🔄 Redirecting to:', response.redirect_url);

                setTimeout(() => {
                    window.location.href = response.redirect_url;
                }, 1000);
            } else {
                console.error('❌ Response not successful:', response);
                showToast('Failed: ' + (response.message || 'Unknown error'));
                $('#confirmBtn').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Konfirmasi');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error creating new session:', xhr);

            let errorMessage = 'Failed to create new session';

            try {
                const response = JSON.parse(xhr.responseText);
                errorMessage = response.message || response.error || errorMessage;

                if (response.errors) {
                    const errorDetails = Object.values(response.errors).flat().join(', ');
                    errorMessage += ': ' + errorDetails;
                }
            } catch (e) {
                if (xhr.status === 422) {
                    errorMessage = 'Validation failed. Please check your input.';
                } else if (xhr.status === 403) {
                    errorMessage = 'You do not have permission to perform this action.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Session or counselor not found.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please try again or contact support.';
                }
            }

            showToast(errorMessage);
            $('#confirmBtn').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Konfirmasi');
        }
    });
}

function deleteSession(sessionId) {
    let confirmMessage = 'Delete this session? It will be removed from your view.';
    showConfirm(confirmMessage, function () {
        console.log('🗑️ Student deleting session:', sessionId);

        $.ajax({
            url: chatRoutes.deleteSession(sessionId), // FIXED: Use route helper
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
                        window.location.href = "{{ route('public.konseling.index') }}";
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

function insertQuickMessage(text) {
    $('input[name="message"]').val(text).focus();
}

let selectedOption = null;

function showNewSessionModal() {
    $('#newSessionModal').modal('show');
}

$(document).on('click', '.session-option-card', function() {
    $('.session-option-card').removeClass('selected');
    $(this).addClass('selected');
    selectedOption = $(this).data('option');
    $('#confirmBtn').prop('disabled', false);
});

$('#newSessionModal').on('hidden.bs.modal', function() {
    $('.session-option-card').removeClass('selected');
    selectedOption = null;
    $('#confirmBtn').prop('disabled', true).html('<i class="fas fa-check me-1"></i> Konfirmasi');
});

    function goBackToSidebar() {
        // Khusus mobile: tampilkan sidebar dan sembunyikan area chat
        const chatContainer = document.querySelector('.chat-container');
        chatContainer.classList.remove('chat-active');
        window.scrollTo(0, 0); // biar balik ke atas
    }
</script>
@endif
@endpush
