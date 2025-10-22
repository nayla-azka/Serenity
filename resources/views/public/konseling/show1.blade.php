@include('public.layouts.navbar')

<div class="container-fluid" style="height: 100vh;">
    <div class="row h-100">

        <!-- Sidebar daftar sesi/guru -->
        <div class="col-3 p-0 border-end" style="background-color: #fbd49b;">
            <div class="list-group list-group-flush">
                    @foreach($allSessions as $s)
                        <a href="{{ route('public.konseling.show', $s->id_session) }}"
                        class="list-group-item list-group-item-action py-3 {{ $s->id_session == $session->id_session ? 'active' : '' }}">
                            <strong>{{ $s->counselor->counselor_name ?? 'Guru BK' }}</strong>
                            <div class="small text-muted">{{ $s->topic }}</div>
                        </a>
                    @endforeach
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-9 d-flex flex-column p-0" style="border-left:1px solid #ddd;">

            <!-- Header Chat -->
            <div class="p-3 fw-bold" style="background-color:#cfd9f9;">
                {{ $session->counselor->counselor_name ?? 'Guru BK' }} - {{ $session->topic }}
            </div>


            <!-- Body Chat -->
            <div class="flex-grow-1 p-3" style="overflow-y:auto; background:white;">
                @foreach($messages as $msg)
                    <div class="mb-2 d-flex {{ $msg->id_sender == auth()->id() ? 'justify-content-end' : '' }}">
                        <div class="p-2 rounded shadow-sm" 
                             style="background: {{ $msg->id_sender == auth()->id() ? '#e0f7fa' : '#f1f1f1' }}; max-width:60%;">
                            {{ $msg->message }}
                            <div class="text-muted small text-end">
                                {{ $msg->created_at->format('H:i') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Input Chat -->
            <form action="{{ route('chat.send') }}" method="POST" class="d-flex border-top">
                @csrf
                <input type="hidden" name="id_session" value="{{ $session->id_session }}">
                <input type="text" name="message" class="form-control border-0" placeholder="Ketik pesan...">
                <button type="submit" class="btn btn-link text-primary">
                    <i class="bi bi-send"></i>
                </button>
            </form>
        </div>

    </div>
</div>
