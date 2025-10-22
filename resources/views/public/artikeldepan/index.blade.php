dashboard.blade.php

<style>
.scroll-container {
    scrollbar-width: thin;
    scrollbar-color: #4d4676 transparent;

    /* Chrome, Edge, Safari */
}
.scroll-container::-webkit-scrollbar {
    height: 6px;
}
.scroll-container::-webkit-scrollbar-track {
    background: transparent;
}
.scroll-container::-webkit-scrollbar-thumb {
    background: #5b256c;
    border-radius: 10px;
    transition: background 0.3s ease;
}
.scroll-container::-webkit-scrollbar-thumb:hover {
    background: #250e2c;
}

        html, body {
            height: 100%;
            min-height: 100vh;
    		background-size: cover;
    		background-position: center;
    		background-repeat: no-repeat;
        }


</style>


<!-- Card besar -->
<br><div class="card shadow-lg mx-4" style="background-color: #837ab67f;">

    <div class="card-body" style="box-shadow: 10px 10px 8px #00000047;">
    <h2 class="mb-3">Forum Relevan</h2>

<div class="d-flex overflow-auto gap-3 pb-2 scroll-container">
    @foreach($artikels as $artikel)
        <div class="card bg-light shadow-sm" style="min-width: 250px; cursor: pointer;"
             onclick="window.location='{{ route('public.artikeldepan.show', $artikel->article_id) }}'">

            @if($artikel->photo)
                <img src="{{ asset('storage/' . $artikel->photo) }}"
                     class="card-img-top"
                     style="height: 150px; object-fit: cover;"
                     alt="{{ $artikel->title }}">
            @endif

            <div class="card-body">
                <h5 class="card-title">{{ $artikel->title }}</h5>
                <p class="card-text text-muted">{{ Str::limit($artikel->content, 80) }}</p>
            </div>
        </div>
    @endforeach
</div>

</div>
    </div><br><br>
