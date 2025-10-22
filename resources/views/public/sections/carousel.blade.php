<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* Default carousel image */
.carousel-item img {
    height: 350px;
    object-fit: cover;
}

/* Tablet */
@media (max-width: 992px) {
    .carousel-item img {
        height: 300px;
    }
}

/* HP */
@media (max-width: 768px) {
    .carousel-item img {
        height: 220px;
    }
    .carousel-caption h5 {
        font-size: 16px;
    }
    .carousel-caption p {
        font-size: 13px;
    }
}

/* HP kecil */
@media (max-width: 576px) {
    .carousel-item img {
        height: 180px;
    }
    .carousel-caption {
        bottom: 10px; /* geser biar gak ketiban tombol navigasi */
    }
    .carousel-caption h5 {
        font-size: 14px;
    }
    .carousel-caption p {
        display: none; /* sembunyiin deskripsi biar gak terlalu penuh */
    }
}
</style>

<div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-indicators">
    @foreach ($banners as $index => $banner)
      <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="{{ $index }}"
        class="{{ $index == 0 ? 'active' : '' }}"></button>
    @endforeach
  </div>

  <div class="carousel-inner">
    @foreach ($banners as $index => $banner)
      <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
        <img src="{{ asset('storage/' . $banner->photo) }}"
             class="d-block w-100" alt="{{ $banner->title }}">
        <div class="carousel-caption d-none d-md-block">
          <h5>{{ $banner->title }}</h5>
          <p>{{ $banner->desc }}</p>
        </div>
      </div>
    @endforeach
  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>
