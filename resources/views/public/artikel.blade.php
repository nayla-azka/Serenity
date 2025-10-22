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



</style>

<div class="container py-5">

    {{-- Kalau ada pencarian --}}
    @if(!empty($query))
        <h5 class="mb-4">Hasil pencarian untuk: <em>{{ $query }}</em></h5>

        <div class="row">
            @forelse($artikels as $artikel)
                <div class="col-md-4 mb-3">
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
                            <p class="card-text text-muted">
                                {{ Str::limit(strip_tags($artikel->content), 100) }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">Tidak ada artikel ditemukan.</p>
            @endforelse
        </div>

        {{-- Pagination untuk hasil search --}}
        <div class="mt-4">
            {{ $artikels->withQueryString()->links() }}
        </div>

    @else
        {{-- Tampilan default (featured + terbaru) --}}

        {{-- Featured Article --}}
        @if($featured)
            <div class="row mb-5">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(rgba(167, 155, 233, 0.75), rgba(245, 186, 222, 0.75));">
                        <div class="row g-0">
                            <div class="col-md-6">
                                <a href="{{ route('public.artikel_show', $featured->article_id) }}">
                                    <img src="{{ asset('storage/' . $featured->photo) }}"
                                        class="img-fluid rounded-start"
                                        alt="{{ $featured->title }}"
                                        style="height:100%; object-fit:cover;">
                                </a>
                            </div>
                            <div class="col-md-6 p-4">
                                <h2 class="fw-bold">{{ $featured->title }}</h2>
                                <p class="text-muted">by: {{ $featured->author_id }}</p>
                                <p>{{ Str::limit(strip_tags($featured->content), 250) }}</p>
                                <a href="{{ route('public.artikel_show', $featured->article_id) }}" class="dt dt-btn create">
                                    Read More
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Latest Articles --}}
        <div class="card shadow-lg mx-2 mb-5" style="background: linear-gradient(rgba(167, 155, 233, 0.6), rgba(245, 186, 222, 0.6));">
            <div class="card-body">
                <h3 class="mb-4 fw-bold text-center">Terbaru</h3>
                <div class="row">
                    @forelse($latest as $artikel)
                        <div class="col-md-4 mb-4">
                            <div class="card bg-serenity h-100 shadow-sm kartubaru">
                                <a href="{{ route('public.artikel_show', $artikel->article_id) }}">
                                    <img src="{{ asset('storage/' . $artikel->photo) }}"
                                        class="card-img-top"
                                        alt="{{ $artikel->title }}"
                                        style="height:200px; object-fit:cover;">
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('public.artikel_show', $artikel->article_id) }}" class="text-decoration-none text-white">
                                            {{ Str::limit($artikel->title, 60) }}
                                        </a>
                                    </h5>
                                    <p class="card-text">
                                        {{ Str::limit(strip_tags($artikel->content), 100) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">Belum ada artikel terbaru.</p>
                    @endforelse
                </div>
            </div>
        </div>

    @endif

</div>
@endsection
