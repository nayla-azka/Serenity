@extends('public.layouts.layout')

@push('styles')
<style>
     html, body {
            height: 100%;
            min-height: 100vh;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: "Segoe UI", sans-serif;

        }

        .form-container {
            background: rgba(189, 181, 235, 0.95);
            padding: 40px;
            border-radius: 20px;
            max-width: 750px;
            margin: 60px auto;
            box-shadow: 0 8px 25px rgba(0,0,0,0.25);
        }

        .form-container h3 {
            font-weight: 700;
            color: #4b4376;
            margin-bottom: 10px;
        }

        .form-container p {
            font-size: 0.95rem;
            color: #333;
        }

        .form-label {
            font-weight: 600;
            color: #4b4376;
        }

        .form-control {
            border-radius: 10px;
            border: 1.5px solid #b2aadf;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #837ab6;
            box-shadow: 0 0 8px rgba(131, 122, 182, 0.5);
        }

        .btn-serenity {
            display: inline-block;
            padding: 0.9em 2.2em;
            border: none;
            border-radius: 50px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            font-size: 15px;
            position: relative;
            overflow: hidden;
            color: #f8f6ff;
            background-color: rgb(131, 122, 182);
            outline: 2px solid #837ab6;
            transition: all 500ms;
            text-decoration: none;
            margin-top: 10px;
        }

        .btn-serenity:hover {
            color: #fff;
            transform: scale(1.07);
            outline: 2px solid #a89ad9;
            box-shadow: 0 8px 20px rgba(131,122,182,0.4);
        }

        .btn-serenity::before {
            content: "";
            position: absolute;
            left: -50px;
            top: 0;
            width: 0;
            height: 100%;
            background-color: #6f63a0;
            transform: skewX(45deg);
            z-index: -1;
            transition: width 500ms;
        }

        .btn-serenity:hover::before {
            width: 250%;
        }

        /* icon back */
        .form-container a {
            display: inline-flex;
            align-items: center;
            margin-bottom: 15px;
            transition: 0.3s;
        }

        .form-container a:hover {
            color: #4b4376 !important;
            transform: translateX(-3px);
        }
        .alert-fixed {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: auto;
            max-width: 600px;
            z-index: 3000; /* Higher than Bootstrap modal */
            pointer-events: none; /* Allow clicks through except on alert */
        }

        .alert-fixed .alert {
            pointer-events: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            border-radius: 0.5rem;
            animation: slideDown 0.4s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translate(-50%, -20px);
            }
            to {
                opacity: 1;
                transform: translate(-50%, 0);
            }
        }



</style>
@endpush

@section('content')
<div id="alert-container" class="alert-fixed"></div>

<div class="form-container mb-3" style="transform: scale(0.85); margin-top: -20px;">
    <div class="section-header text-black">
        <div class="section-header-back">
          <a href="{{ url('/') }}" class="btn-back">
            <span class="btn-back__icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" height="20" width="20">
                <path d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z" fill="#fff"/>
                <path d="m237.248 512 265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z" fill="#fff"/>
              </svg>
            </span>
          </a>
        </div>
      </div>
    <h3 class="fw-bold">Form Laporan</h3>
    <p><strong>Lihat Sesuatu? Ceritakan di sini</strong><br>
    Kalau kamu menemukan hal yang melanggar aturan, laporkan di sini.<br>
    Kamu bisa memilih untuk menyembunyikan ataupun menyertakan identitasmu.<br>
    Tolong tulis sejelas mungkin agar tim bk bisa membantu menyelesaikannya secepat mungkin.</p>

    <form action="{{ route('public.lapor.store') }}" method="POST">
        @csrf
        <div class="row mb-3">
            <!-- Kolom kiri -->
            <div class="col-md-6">
                <label class="form-label">Masalah</label>
                <input type="text" name="topic" class="form-control input-style" required>

                <label class="form-label mt-3">Tanggal Kejadian</label>
                <input type="date" name="date" class="form-control input-style" required>

                <label class="form-label mt-3">Tempat Kejadian</label>
                <input type="text" name="place" class="form-control input-style" required><br>

            </div>

            <!-- Kolom kanan -->
            <div class="col-md-6">
                <label class="form-label">Kronologis Lengkap</label>
                <textarea name="chronology" class="form-control form-control" rows="8" required></textarea>
            </div>
        </div>

        <div class="form-check">
        <input class="form-check-input" type="checkbox" name="sembunyikan_identitas" value="1" id="hideIdentity">
        <label class="form-check-label" for="hideIdentity">
            Sembunyikan identitas?
        </label>
        </div><br>

        <button type="submit" class="dt dt-btn create">Submit</button>
    </form>
</div>
@endsection
