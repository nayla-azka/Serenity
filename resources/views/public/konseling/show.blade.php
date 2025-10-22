@extends('public.layouts.layout')

@section('content')

<div class="container-fluid vh-100">
    <div class="row h-100">

        <!-- Sidebar Guru -->
        <div class="col-3 p-0 border-end" style="background-color: #fbd49b; overflow-y:auto;">
            @foreach($allSessions as $s)
                <a href="{{ route('public.konseling.show', $s->id_session) }}"
                   class="d-flex align-items-center px-3 py-3 border-bottom text-decoration-none {{ $s->id_session == $session->id_session ? 'bg-light' : 'text-dark' }}">
                    <img src="{{ asset('storage/' . $s->counselor->photo) }}"
                         class="rounded-circle me-3" width="40" height="40" style="object-fit:cover;">
                    <div>
                        <strong>{{ $s->counselor->counselor_name }}</strong><br>
                        <small class="text-muted">{{ $s->counselor->kelas }}</small>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Chat Area -->
        <div class="col-9 d-flex flex-column p-0">

            <!-- Header -->
            <div class="p-3 border-bottom bg-white d-flex align-items-center">
                <img src="{{ asset('storage/' . $session->counselor->photo) }}"
                     class="rounded-circle me-3" width="40" height="40" style="object-fit:cover;">
                <div>
                    <h6 class="mb-0">{{ $session->counselor->counselor_name }}</h6>
                    <small class="text-muted">{{ $session->counselor->kelas }}</small>
                </div>
            </div>

            <!-- Chat Messages -->
            <div id="chat-box" class="flex-grow-1 p-3" style="overflow-y:auto; background:#fafafa;">
                <!-- AJAX load -->
            </div>

            <!-- Input -->
            <div class="p-2 border-top bg-white">
                <form id="chat-form" class="d-flex">
                    @csrf
                    <input type="hidden" name="id_session" value="{{ $session->id_session }}">
                    <input type="text" name="message" class="form-control me-2" placeholder="Ketik pesan..." required>
                    <button class="btn btn-outline-primary rounded-circle">
                        <i class="bi bi-send"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Script --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let sessionId = {{ $session->id_session }};
let chatBox = $('#chat-box');

function loadMessages() {
    $.get("{{ route('chat.fetch', ':id') }}".replace(':id', sessionId), function(data) {
        chatBox.empty();
        data.forEach(msg => {
            let isStudent = msg.sender_type === 'student';
            let align = isStudent ? 'text-start' : 'text-end';
            let bubbleColor = isStudent ? '#fff' : '#DCF8C6';
            chatBox.append(`
                <div class="mb-2 ${align}">
                    <div class="d-inline-block px-3 py-2 rounded" style="background:${bubbleColor}; max-width:60%;">
                        ${msg.message}
                    </div><br>
                    <small class="text-muted">${msg.sent_at}</small>
                </div>
            `);
        });
        chatBox.scrollTop(chatBox[0].scrollHeight);
    });
}

$('#chat-form').on('submit', function(e) {
    e.preventDefault();
    $.post("{{ route('chat.send') }}", $(this).serialize(), function() {
        $('input[name="message"]').val('');
        loadMessages();
    });
});

setInterval(loadMessages, 3000);
loadMessages();
</script>
@endsection
