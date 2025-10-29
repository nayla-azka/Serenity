<style>

.scroll-container {
    scrollbar-width: thin;
    scrollbar-color: #4d4676 transparent;
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
    /* HAPUS flex-direction: row-reverse; */
}

/* Scrollbar untuk Chrome, Edge, Safari */
.scroll-container::-webkit-scrollbar {
    height: 6px;
}
.scroll-container::-webkit-scrollbar-track {
    background: transparent;
}
.scroll-container::-webkit-scrollbar-thumb {
    background: #5b256c;
    border-radius: 6px;
    transition: background 0.3s ease;
}
.scroll-container::-webkit-scrollbar-thumb:hover {
    background: #250e2c;
}

html, body {
    height: 100%;
    margin: 0;
    min-height: 100vh;
    background: linear-gradient(rgb(184, 172, 235), rgb(246, 238, 255));
    background-attachment: fixed;
}

/* Default card style */
.scroll-container .card {
    border-radius: 8px;
    transition: all 0.2s ease;
    box-shadow: 0 1px 4px rgba(0,0,0,0.15);
    min-width: 220px;
    max-width: 220px;
    max-height: 450px;
    font-size: 0.8rem;
    flex-shrink: 0; /* Biar card ga menciut */
}

.scroll-container .card:hover {
    transform: scale(1.03);
    box-shadow:
        0 0 10px rgba(131, 122, 182, 0.8),
        0 0 18px rgba(131, 122, 182, 0.5),
        0 0 25px rgba(131, 122, 182, 0.3);
}

.bg-serenity {
    color: rgb(248, 246, 255) !important;
    background-color: #250e2cac !important;
}

/* Judul & teks */
.scroll-container .card-title {
    font-size: 1rem;
    margin-bottom: 0.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Maksimal 2 baris */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.4;
    min-height: 2.8rem; /* 2 baris * 1.4 line-height */
}
.scroll-container .card-text {
    font-size: 0.8rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 3; /* Maksimal 3 baris untuk konten */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Gambar */
.scroll-container .card-img-top {
    height: 180px;
    object-fit: cover;
    border-radius: 8px 8px 0 0;
}

/* HAPUS .flex-fix karena ga perlu lagi */

/* ✅ Responsive tweaks */

/* Tablet */
@media (max-width: 992px) {
    .scroll-container .card {
        min-width: 200px;
        max-width: 230px;
        font-size: 0.75rem;
    }
    .scroll-container .card-img-top {
        height: 100px;
    }
}

/* HP */
@media (max-width: 768px) {
    .scroll-container .card {
        min-width: 160px;
        max-width: 160px;
        font-size: 0.7rem;
        height: 280px !important;
    }
    .scroll-container .card-title {
        font-size: 0.8rem;
    }
    .scroll-container .card-text {
        font-size: 0.65rem;
    }
    .scroll-container .card-img-top {
        height: 140px;
    }

    #card-besar {
        margin-left: 0px !important;
        margin-right: 0px !important;
    }
}

/* HP kecil */
@media (max-width: 576px) {
    .scroll-container {
        gap: 0.5rem;
    }
    .scroll-container .card {
        min-width: 140px;
        max-width: 160px;
        height: 280px; /* Sesuaikan untuk mobile */
        font-size: 0.65rem;
    }
    .scroll-container .card-title {
        font-size: 0.75rem;
        -webkit-line-clamp: 2;
        min-height: 2.1rem;
    }
    .scroll-container .card-text {
        font-size: 0.65rem;
        -webkit-line-clamp: 3;
    }
    .scroll-container .card-img-top {
        height: 120px;
    }
}

</style>

<!-- Card besar -->
<br><div class="card shadow-lg mx-4" style="background: linear-gradient(rgba(167, 155, 233, 0.6), rgba(245, 186, 222, 0.6));">
    <div class="card-body" style="box-shadow: 10px 10px 8px #00000047;">
        <h2 class="mb-3 text-center" style="font-size: 25px; text-align: center;">Forum Relavan</h2>

        <div class="overflow-auto gap-3 pb-2 scroll-container" id="card-besar" style="margin-left: 15px; margin-right: 15px">
    @foreach($artikels as $artikel)
        <a href="{{ route('public.artikel_show', $artikel->article_id) }}" style="text-decoration: none; color: inherit;">
            <div class="card bg-serenity shadow-sm" style="width: 220px; height: 310px; cursor: pointer; display: flex; flex-direction: column;">
                @if($artikel->photo)
                    <img src="{{ asset('storage/' . $artikel->photo) }}"
                         class="card-img-top"
                         alt="{{ $artikel->title }}"
                         style="object-fit: cover; height: 150px; width: 100%; object-position: center; flex-shrink: 0;">
                @endif

                <div class="card-body" style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
                    <h5 class="card-title" style="margin-bottom: 0.5rem;">{{ $artikel->title }}</h5>
                    <p class="card-text" style="flex: 1; overflow: hidden;">{{ Str::limit(strip_tags($artikel->content), 80) }}</p>
                </div>
            </div>
        </a>
    @endforeach
</div>
    </div>
</div><br><br>