@extends('public.layouts.layout')

@section('content')
<style>
html, body {
    height: 100%;
    min-height: 100vh;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

/* Mobile compact layout for unfeatured articles */
@media (max-width: 767px) {
    .mobile-compact-card {
        display: flex;
        flex-direction: row;
        margin-bottom: 0.5rem;
        overflow: hidden;
        border-radius: 12px;
    }

    .mobile-compact-card img {
        width: 110px;
        height: 110px;
        object-fit: cover;
        flex-shrink: 0;
        border-radius: 8px 0 0 8px;
    }

    .mobile-compact-card .card-body {
        padding: 0.875rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .mobile-compact-card .card-title {
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 0.4rem;
        line-height: 1.3;
    }

    .mobile-compact-card .card-text {
        font-size: 0.8rem;
        margin-bottom: 0;
        line-height: 1.4;
        opacity: 0.9;
    }

    /* Featured article mobile adjustments */
    .featured-card-mobile {
        margin-bottom: 1rem;
        border-radius: 12px;
        overflow: hidden;
    }

    .featured-card-mobile img {
        height: 180px !important;
        width: 100%;
        object-fit: cover;
        border-radius: 12px 12px 0 0;
    }

    .featured-card-mobile .featured-content {
        padding: 1rem !important;
    }

    .featured-card-mobile h2 {
        font-size: 1.2rem;
        margin-bottom: 0.4rem;
    }

    .featured-card-mobile p {
        font-size: 0.85rem;
        line-height: 1.5;
    }

    .featured-card-mobile .text-muted {
        font-size: 0.75rem;
        margin-bottom: 0.4rem;
    }

    /* Latest articles section mobile */
    .latest-section-mobile {
        border-radius: 12px;
        padding: 1rem !important;
    }

    .latest-section-mobile h3 {
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }
}

/* Desktop styles */
@media (min-width: 768px) {
    .card {
        border-radius: 12px;
        overflow: hidden;
    }

    .card img {
        border-radius: 0;
    }

    .featured-card-mobile .col-md-5 img {
        border-radius: 12px 0 0 12px;
        max-height: 280px;
    }

    .latest-section-mobile h3 {
        margin: 0rem;
    }
}

/* General card improvements */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

/* .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
} */

.card img {
    transition: transform 0.3s ease;
}

.card:hover img {
    transform: scale(1.05);
}

.card a {
    display: block;
    overflow: hidden;
}
</style>

<div class="container py-3 py-md-5">

    {{-- Kalau ada pencarian --}}
    @if(!empty($query))
        <h5 class="mb-3 mb-md-4">Hasil pencarian untuk: <em>{{ $query }}</em></h5>

        <div class="row">
            @forelse($artikels as $artikel)
                <div class="col-12 col-md-6 col-lg-4 mb-3 d-none d-md-block">
                    <div class="card h-100 shadow-sm">
                        <a href="{{ route('public.artikel_show', $artikel->article_id) }}">
                            <img src="{{ asset('storage/' . $artikel->photo) }}"
                                 class="card-img-top"
                                 alt="{{ $artikel->title }}"
                                 style="height:200px; object-fit:cover;">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('public.artikel_show', $artikel->article_id) }}"
                                   class="text-decoration-none text-dark">
                                    {{ Str::limit($artikel->title, 60) }}
                                </a>
                            </h5>
                            <p class="card-text text-muted small mb-0">
                                by: {{ $artikel->author->name ?? 'Unknown' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Mobile compact layout --}}
                <div class="col-12 d-md-none px-2">
                    <div class="card mobile-compact-card shadow-sm">
                        <a href="{{ route('public.artikel_show', $artikel->article_id) }}">
                            <img src="{{ asset('storage/' . $artikel->photo) }}"
                                 alt="{{ $artikel->title }}">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('public.artikel_show', $artikel->article_id) }}"
                                   class="text-decoration-none text-dark">
                                    {{ Str::limit($artikel->title, 50) }}
                                </a>
                            </h5>
                            <p class="card-text small mb-0">
                                by: {{ $artikel->author->name ?? 'Unknown' }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">Tidak ada artikel ditemukan.</p>
            @endforelse
        </div>

        {{-- Pagination untuk hasil search --}}
        <div class="mt-3 mt-md-4">
            {{ $artikels->withQueryString()->links() }}
        </div>

    @else
        {{-- Tampilan default (featured + terbaru) --}}

        {{-- Featured Article --}}
        @if($featured)
            <div class="row mb-3 mb-md-5">
                <div class="col-12">
                    <div class="card border-0 shadow featured-card-mobile" style="background: linear-gradient(135deg, rgba(167, 155, 233, 0.85), rgba(245, 186, 222, 0.85));">
                        <div class="row g-0">
                            <div class="col-12 col-md-5">
                                <a href="{{ route('public.artikel_show', $featured->article_id) }}">
                                    <img src="{{ asset('storage/' . $featured->photo) }}"
                                        class="img-fluid w-100"
                                        alt="{{ $featured->title }}"
                                        style="height:100%; min-height: 200px; max-height: 280px; object-fit:cover;">
                                </a>
                            </div>
                            <div class="col-12 col-md-7 featured-content p-3 p-md-4 d-flex flex-column justify-content-center">
                                <h2 class="fw-bold mb-2" style="font-size: 1.5rem;">{{ $featured->title }}</h2>
                                <p class="text-muted mb-2 mb-md-3" style="font-size: 0.85rem;">by: {{ $featured->author->name ?? 'Unknown' }}</p>
                                <p class="mb-3" style="font-size: 0.9rem;">{{ Str::limit(strip_tags($featured->content), 150) }}</p>
                                <div>
                                    <a href="{{ route('public.artikel_show', $featured->article_id) }}"
                                       class="btn-serenity px-4 rounded-pill"
                                       style="text-decoration:none; text-align:center;">
                                        Read More
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Latest Articles --}}
        <div class="card shadow-lg mx-0 mx-md-2 mb-3 mb-md-5 latest-section-mobile" style="background: linear-gradient(135deg, rgba(167, 155, 233, 0.5), rgba(245, 186, 222, 0.5)); border-radius: 16px;">
            <div class="card-body p-2 p-md-3">
                <h3 class="m-md-2 fw-bold">Terbaru</h3>
                <div class="row g-md-3">
                    @forelse($latest as $artikel)
                        {{-- Desktop layout --}}
                        <div class="col-12 col-md-6 col-lg-4 mb-1 d-none d-md-block">
                            <div class="card h-100 shadow-sm kartubaru" style="background: #250e2ca9 !important;">
                                <a href="{{ route('public.artikel_show', $artikel->article_id) }}">
                                    <img src="{{ asset('storage/' . $artikel->photo) }}"
                                        class="card-img-top"
                                        alt="{{ $artikel->title }}"
                                        style="height:200px; object-fit:cover;">
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('public.artikel_show', $artikel->article_id) }}"
                                           class="text-decoration-none text-white fw-semibold">
                                            {{ Str::limit($artikel->title, 60) }}
                                        </a>
                                    </h5>
                                    <p class="card-text text-white small mb-2" style="opacity: 0.85;">
                                        by: {{ $artikel->author->name ?? 'Unknown' }}
                                    </p>
                                    <p class="card-text text-white" style="opacity: 0.9; font-size: 0.875rem;">
                                        {{ Str::limit(strip_tags($artikel->content), 100) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Mobile compact layout --}}
                        <div class="col-12 d-md-none px-1">
                            <div class="card mobile-compact-card shadow-sm" style="background: #250e2ca9;">
                                <a href="{{ route('public.artikel_show', $artikel->article_id) }}">
                                    <img src="{{ asset('storage/' . $artikel->photo) }}"
                                         alt="{{ $artikel->title }}">
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('public.artikel_show', $artikel->article_id) }}"
                                           class="text-decoration-none text-white fw-semibold">
                                            {{ Str::limit($artikel->title, 50) }}
                                        </a>
                                    </h5>
                                    <p class="card-text text-white small mb-0">
                                        by: {{ $artikel->author->name ?? 'Unknown' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted px-2">Belum ada artikel terbaru.</p>
                    @endforelse
                </div>
            </div>
        </div>

    @endif

</div>
@endsection
