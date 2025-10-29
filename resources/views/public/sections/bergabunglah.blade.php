<style>
.btn-serenity {
  display: inline-block;
  padding: 0.4em 1em; /* lebih kecil */
  border: none;
  border-radius: 30px; /* masih rounded-pill tapi mini */
  font-weight: 600;
  letter-spacing: 1px;
  text-transform: uppercase;
  cursor: pointer;
  font-size: 0.7rem; /* ≈ 11px */
  position: relative;
  overflow: hidden;
  color: #f8f6ff;
  background-color: rgb(131, 122, 182);
  outline: 1.5px solid #837ab6;
  transition: all 400ms;
  text-decoration: none;
}

.btn-serenity:hover {
  color: #fff;
  transform: scale(1.05);
  outline: 1.5px solid #a89ad9;
}

.btn-serenity::before {
  content: "";
  position: absolute;
  left: -40px;
  top: 0;
  width: 0;
  height: 100%;
  background-color: #6f63a0;
  transform: skewX(45deg);
  z-index: -1;
  transition: width 400ms;
}

.btn-serenity:hover::before {
  width: 200%;
}

/* Card mini */
.card-mini {
  background: linear-gradient(135deg, rgba(167, 155, 233, 0.8), rgba(245, 186, 222, 0.8));
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.12);
}
.card-mini .card-body {
  padding: 0.8rem; /* lebih ramping */
  border-radius: 10px;
}
.card-mini p {
  font-size: 0.7rem; /* teks ≈ 11px */
  line-height: 1.3;
  margin-bottom: 0;
}

/* Responsive untuk HP */
@media (max-width: 700px) {
  .card-mini .card-body {
    flex-direction: column; /* teks di atas, tombol di bawah */
    text-align: center;     /* biar rapi tengah */
    gap: 0.6rem;            /* kasih jarak antar elemen */
  }

  .card-mini p {
    font-size: 0.8rem; /* sedikit lebih gede biar kebaca jelas di HP */
  }

  .btn-serenity {
    width: 80%;        /* tombol full lebar di HP */
    font-size: 0.75rem; /* sedikit lebih besar */
    padding: 0.5em 1em;
  }
}

</style>

<br>
@guest
<div class="card card-mini shadow-lg mx-4 border-0">
  <div class="card-body d-flex justify-content-between align-items-center">

    <p class="fw-semibold" style="color: #1e293b;">
      Apakah kamu siswa <span class="fw-bold text-primary">SMKN 13 Bandung</span> yang ingin melakukan Konseling Digital ataupun Melaporkan Sesuatu? <br>
      <span class="text-dark">Bergabunglah sekarang!</span>
    </p>

    <a href="/serenity/login" class="btn-serenity">Log In</a>
  </div>
</div><br>
@endguest

