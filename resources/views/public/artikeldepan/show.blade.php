{{-- resources/views/public/artikeldepan/show.blade.php --}}
@extends('public.layouts.layout')

@section('content')
<div class="container py-4">
    <h1>{{ $artikels->title ?? $artikels->judul }}</h1>
    
    @if($artikels->photo ?? $artikels->gambar)
        <img src="{{ asset('storage/' . ($artikels->photo ?? $artikels->gambar)) }}" 
             class="img-fluid rounded mb-3" 
             alt="{{ $artikels->title ?? $artikels->judul }}">
    @endif
    
    <p class="fs-5">{{ $artikels->content ?? $artikels->isi }}</p>

    <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">← Back</a>
</div>
@endsection
