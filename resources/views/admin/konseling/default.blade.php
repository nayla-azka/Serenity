@extends('admin.layouts.layout')
@push('styles')
<style>
    /* Section Header */
    .section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 2px solid #e5e7eb;
    }
    .section-header h2 {
        font-weight: 700;
        color: #2d2d44;
    }

    /* Card */
    .card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .card-header {
        background: linear-gradient(135deg, #837ab6, #8177d3);
        border-radius: 1rem 1rem 0 0 !important;
        color: #fff;
    }
    .card-header .card-title {
        margin: 0;
        font-weight: 600;
    }

    /* Form */
    .form-check-label strong {
        color: #2d2d44;
    }
    textarea.form-control {
        border-radius: 0.75rem;
        border: 1px solid #d1d5db;
        resize: none;
    }
    textarea:focus {
        border-color: #6c63ff;
        box-shadow: 0 0 0 0.2rem rgba(108, 99, 255, 0.25);
    }

    /* Template Buttons */
    .template-btn {
        width: 100%;
        border-radius: 0.75rem;
        font-weight: 500;
        transition: 0.2s;
    }
    .template-btn:hover {
        background: #250e2c;
        color: #fff;
    }

    /* Preview */
    #message-preview {
        font-size: 0.95rem;
        line-height: 1.5;
    }


    .btn-outline-serenity {
  color: #250e2c;              /* warna teks */
  border: 1px solid #250e2c;   /* warna border */
  background-color: transparent;
  border-radius: 10px;         /* sudut membulat */
  transition: all 0.3s ease;   /* animasi halus */
}

.btn-outline-serenity:hover {
  color: #fff;                 /* teks jadi putih */
  background-color: #250e2c;   /* isi background */
  border-color: #250e2c;       /* border tetap sama */
  box-shadow: 0 4px 10px rgba(74, 78, 105, 0.4); /* efek bayangan */
}

.btn-outline-serenity:active {
  transform: scale(0.97);      /* efek klik */
}


</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Section Header -->
    <div class="section-header text-black mb-4">
        <div class="section-header-back">
            <a href="{{ url('admin/dashboard') }}" class="btn-back">
                <span class="btn-back__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" height="20" width="20">
                        <path d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z" fill="#fff"/>
                        <path d="m237.248 512 265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z" fill="#fff"/>
                    </svg>
                </span>
            </a>
        </div>
        <h2 class="ms-2">Pengaturan Pesan Otomatis</h2>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card" style="background-color: rgb(222, 220, 240);">
                <div class="card-header">
                    <h5 class="card-title fas fa-comment-dots m-0"> Pesan Sambutan Otomatis</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.pesan.update-settings') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Profil Konselor -->
                            <div class="col-lg-4 mb-4">
                                <div class="card bg-light h-100">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted">Informasi Profil</h6>
                                        <p><strong>Nama:</strong> {{ $counselor->counselor_name }}</p>
                                        <p><strong>NIP:</strong> {{ $counselor->nip }}</p>
                                        <p><strong>Kelas:</strong> {{ $counselor->kelas }}</p>
                                        <p><strong>Kontak:</strong> {{ $counselor->contact }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Pengaturan Pesan -->
                            <div class="col-lg-8">
                                <br>
                                <!-- Textarea -->
                                <div class="form-group">
                                    <label for="default_chat_message">
                                        <i class="fas fa-edit"></i> Pesan Sambutan
                                        <small class="text-muted">(Maksimal 500 karakter)</small>
                                    </label>
                                    <textarea class="form-control"
                                              name="default_chat_message"
                                              id="default_chat_message"
                                              rows="5"
                                              maxlength="500"
                                              placeholder="Masukkan pesan sambutan...">{{ old('default_chat_message', $counselor->default_chat_message) }}</textarea>
                                    <small class="form-text text-muted">
                                        <span id="char-count">{{ strlen($counselor->default_chat_message ?? '') }}</span>/500 karakter
                                        <br>
                                    </small>
                                </div>

                                <!-- Preview -->
                                <div class="alert alert-info mt-3">
                                    <h6><i class="fas fa-eye"></i> Preview Pesan:</h6>
                                    <div class="border rounded p-3 bg-white" id="message-preview">
                                        @if($counselor->default_chat_message)
                                            {{ $counselor->default_chat_message }}
                                        @else
                                            <em class="text-muted">Halo! Selamat datang di ruang konseling. Saya {{ $counselor->counselor_name }}, siap membantu Anda. Bagaimana kabar Anda hari ini? Ada yang bisa saya bantu?</em>
                                        @endif
                                    </div>
                                </div>

                                <!-- Template Buttons -->
                                <div class="mt-3">
                                    <h6><i class="fas fa-lightbulb"></i> Pilih Template Cepat:</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-outline-serenity btn-sm template-btn"
                                                data-template="Halo! Selamat datang di ruang konseling. Saya (masukkan nama), siap membantu Anda. Bagaimana kabar Anda hari ini? Ada yang bisa saya bantu?">
                                            Sambutan Umum
                                        </button>
                                        <button type="button" class="btn btn-outline-serenity btn-sm template-btn"
                                                data-template="Hai! Terima kasih sudah memilih konseling dengan saya. Saya (masukkan nama), dan saya di sini untuk mendengarkan Anda. Ceritakan apa yang sedang Anda rasakan.">
                                            Sambutan Hangat
                                        </button>
                                        <button type="button" class="btn btn-outline-serenity btn-sm template-btn"
                                                data-template="Selamat datang di ruang konseling yang aman dan terpercaya. Saya (masukkan nama). Mari kita mulai dengan berbagi cerita Anda. Apa yang ingin Anda bicarakan hari ini?">
                                            Sambutan Profesional
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Tombol -->
                        <div class="text-end">
                            <button type="submit" class="dt dt-btn create">
                                <i class="fas fa-save me-1"></i> Simpan Pengaturan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Character counter
    $('#default_chat_message').on('input', function() {
        const current = $(this).val().length;
        $('#char-count').text(current);

        if (current > 450) {
            $('#char-count').addClass('text-warning');
        } else if (current > 500) {
            $('#char-count').addClass('text-danger');
        } else {
            $('#char-count').removeClass('text-warning text-danger');
        }

        updatePreview();
    });

    // Template buttons
    $('.template-btn').on('click', function() {
        const template = $(this).data('template');
        $('#default_chat_message').val(template);
        $('#default_chat_message').trigger('input');
    });

    // Preview update
    function updatePreview() {
        const message = $('#default_chat_message').val();
        const counselorName = '{{ $counselor->counselor_name }}';

        if (message.trim() === '') {
            $('#message-preview').html('<em class="text-muted">Halo! Selamat datang di ruang konseling. Saya (masukkan nama), siap membantu Anda. Bagaimana kabar Anda hari ini? Ada yang bisa saya bantu?</em>');
        } else {
            const previewText = message.replace(/{nama}/g, counselorName);
            $('#message-preview').text(previewText);
        }
    }
});
</script>
@endpush
