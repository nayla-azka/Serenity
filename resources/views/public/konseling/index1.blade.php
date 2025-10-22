@include('public.layouts.navbar')

<div class="container-fluid" style="height: 100vh;">
    <div class="row h-100">

        <!-- Sidebar daftar Guru / Sesi -->
        <div class="col-3 p-0 border-end" style="background-color: #fbd49b;">
            <div class="list-group list-group-flush">
                @foreach($sessions as $session)
                    <a href="{{ route('public.konseling.show', $session->id_session) }}"
                    class="list-group-item list-group-item-action py-3">
                        <strong>{{ $session->counselor->counselor_name ?? 'Guru BK' }}</strong>
                        <div class="small text-muted">{{ $session->topic ?? 'Sesi Konseling' }}</div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Area kosong kalau belum pilih sesi -->
        <div class="col-9 d-flex flex-column p-0 align-items-center justify-content-center">
            <div class="text-center text-muted">
                <h4>Selamat datang di Ruang Konseling Digital</h4>
                <p>Pilih guru atau sesi untuk memulai percakapan 💙</p>
            </div>
        </div>

    </div>
</div>
