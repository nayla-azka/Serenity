<style>
.card-profil {
  display: flex;
  flex-direction: column;
  height: 450px; /* tinggi fix */
  overflow: hidden; /* biar ga ada gap aneh */
}

.card-profil:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.35);
    background-color: #372142e0 !important;
}

/* Foto lebih kecil */
.card-profil img {
    width: 100%;
    height: 250px; /* fix tinggi desktop */
    object-fit: cover;
    border-radius: 5px 5px 0 0; /* nyatu sama card */
    display: block; /* hilangin default gap inline img */
    margin: 0;      /* pastiin ga ada margin */
    padding: 0;     /* pastiin ga ada padding */
}


/* Header lebih rapat */
.card-profil .card-header {
    padding: 0.5rem 0.8rem;
}

.card-profil .card-title {
    font-size: 0.95rem;
    margin-bottom: 2px;
}

.card-profil small {
    font-size: 0.75rem;
}

/* Body lebih rapat */
.card-profil .card-body {
    padding: 0.5rem 0.8rem;
}

.card-profil p {
    font-size: 0.8rem;
    margin-bottom: 0.3rem;
    line-height: 1.3;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.show-more {
    color: #ffb6c1;
    cursor: pointer;
    border: none;
    background: none;
}

.show-more:hover {
    text-decoration: underline;
}
.card-img-top {
    margin-bottom: 0 !important;
}

/* Responsive untuk HP */
@media (max-width: 768px) {
    .card-profil {
        flex-direction: row !important;
        height: auto !important;
        min-height: 200px;
    }

    .card-profil img {
        width: 130px !important;
        height: 200px !important; /* fix tinggi mobile */
        min-height: 250px; /* fix tinggi desktop */
        border-radius: 5px 0 0 5px !important;
        object-fit: cover;
        display: block;
        margin: 0;
        padding: 0;

    }

    .card-profil .card-header {
        padding: 0.7rem 0.8rem 0.3rem 0.8rem;
    }

    .card-profil .card-body {
        padding: 0.3rem 0.8rem 0.7rem 0.8rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .card-profil .card-title {
        font-size: 0.9rem;
        margin-bottom: 0.2rem;
        word-wrap: break-word;
        overflow-wrap: break-word;
        line-height: 1.3;
    }

    .card-profil small {
        font-size: 0.7rem;
    }

    .card-profil p {
        font-size: 0.72rem;
        margin-bottom: 0.4rem;
        word-wrap: break-word;
        overflow-wrap: break-word;
        line-height: 1.4;
    }

    .card-profil .desc-text {
        display: block; /* Hapus line-clamp biar bisa expand */
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .card-profil .desc-text.collapsed {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .show-more {
        font-size: 0.68rem !important;
        margin-bottom: 0.4rem !important;
    }

    .mobile-content-wrapper {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 0;
    }

    .card-profil .desc-section {
        flex: 1 1 auto; /* Biar bisa grow */
    }

    .card-profil .contact-section {
        margin-top: auto !important;
        padding-top: 0.5rem;
    }

    .card-profil .contact-section p {
        font-size: 0.7rem !important;
    }

    .card-profil .contact-section svg {
        width: 13px;
        height: 13px;
    }
}

</style>

@auth
<div class="card shadow-lg mx-4" style="background: linear-gradient(rgba(167, 155, 233, 0.6), rgba(245, 186, 222, 0.6));">
  <div class="card-body" style="box-shadow: 10px 10px 8px #00000047;">
    <h2 class="mb-3 text-center" style="font-size: 25px">Profil Guru Bimbingan dan Konseling</h2><br>

    <div class="d-flex gap-5 flex-wrap justify-content-center">
@foreach($guru as $g)
  <div class="col-12 col-md-6 col-lg-3">
    <div class="card card-profil d-flex flex-column"
         style="color: #ffd4da; background-color: #250e2cac; height: 450px;">

      {{-- Foto --}}
      @if($g->photo)
        <img class="card-img-top"
             src="{{ asset('storage/' . $g->photo) }}"
             alt="{{ $g->counselor_name }}">

      @else
        <img class="card-img-top" style="border-radius: 18px;"
             src="{{ asset('images/default-profile.png') }}"
             alt="Default">
      @endif

      <div class="mobile-content-wrapper">
          <div class="card-header px-4 pt-4">
            <h5 class="card-title mb-0">{{ $g->counselor_name }}</h5>
            <small>Kelas: {{ $g->kelas }}</small>
          </div>

          <div class="card-body px-4 pt-2 d-flex flex-column flex-grow-1">
              <div class="desc-section">
                  @php
                      $desc = $g->desc ?? 'Belum ada deskripsi.';
                      $shortDesc = strlen($desc) > 70 ? substr($desc, 0, 70) . '...' : $desc;
                      $isLong = strlen($desc) > 70;
                  @endphp
                  <p class="desc-text mb-2" data-full="{{ $desc }}" data-short="{{ $shortDesc }}">
                      {{ $shortDesc }}
                  </p>
                  @if($isLong)
                      <button class="btn btn-link p-0 text-decoration-none show-more mb-3" style="font-size: 0.8rem; text-align: left;">Show more</button>
                  @endif
              </div>


              <div class="contact-section mt-auto">
                  <p class="mb-0">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                           fill="currentColor" class="bi bi-telephone-fill me-2" viewBox="0 0 16 16">
                          <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                      </svg>
                      <strong>Kontak:</strong> {{ $g->contact ?? '-' }}
                  </p>
              </div>

          </div>
      </div>
    </div>
  </div>
@endforeach
    </div>
  </div>
</div>
@endauth

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.show-more').forEach(function (btn) {
        // Set initial state
        const descText = btn.closest('.desc-section').querySelector('.desc-text');
        descText.classList.add('collapsed');

        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const p = this.closest('.desc-section').querySelector('.desc-text');
            const fullText = p.getAttribute('data-full');
            const shortText = p.getAttribute('data-short');

            if (p.classList.contains('collapsed')) {
                p.textContent = fullText;
                p.classList.remove('collapsed');
                this.textContent = "Show less";
            } else {
                p.textContent = shortText;
                p.classList.add('collapsed');
                this.textContent = "Show more";
            }
        });
    });
});
</script>
