@extends('admin.layouts.layout')

@section('content')
<section class="section">
    <div class="section-header text-black">
      <div class="section-header-back">
          <a href="{{ url('admin/artikel') }}" class="btn-back">
            <span class="btn-back__icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" height="20" width="20">
                <path d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z" fill="#fff"/>
                <path d="m237.248 512 265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z" fill="#fff"/>
              </svg>
            </span>
          </a>
        </div><br>
        <h2>Tambah Artikel</h2>

    </div>

    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card" style="background-color: rgb(222, 220, 240);">
            <div class="card-header">
              <h5 class="bi bi-pencil-square"> Add Section</h5>
            </div>
            <div class="card-body">
                <form action="{{route('admin.artikel.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3 fw-bold">thumbnail</label>
                        <div class="col-sm-12 col-md-7">
                            <div id="image-preview"
                                class="image-preview border rounded d-flex align-items-center justify-content-center"
                                style="width: 400px; height: 200px; background-size: cover; background-position: center center;">
                                <span class="text-muted">No Image</span>
                            </div>
                            <input type="file" name="photo" id="image-upload" class="form-control mt-2 input-style" accept="image/*" />
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3 fw-bold">Judul</label>
                      <div class="col-sm-12 col-md-7">
                        <input type="text" name="title" class="form-control input-style" value="">
                      </div>
                    </div>

                    <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3 fw-bold">Konten</label>
                        <div class="col-sm-12 col-md-7">
                          <textarea name="content" id="editor" class="form-control input-style"></textarea>
                      </div>
                    </div>


                    <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                      <div class="col-sm-12 col-md-7">
                        <button class="dt-btn create">Buat</button>
                      </div>
                    </div>
                </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
$(document).ready(function () {
    // Image preview
    $('#image-upload').on('change', function () {
        let input = this;
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('#image-preview')
                    .css('background-image', 'url(' + e.target.result + ')')
                    .html(''); // remove "No Image" text
            };
            reader.readAsDataURL(input.files[0]);
        }
    });

    // Initialize CKEditor with upload support
    let editor;
    ClassicEditor
        .create(document.querySelector('#editor'), {
            ckfinder: {
                uploadUrl: "{{ route('admin.artikel.upload') }}?_token={{ csrf_token() }}"
            }
        })
        .then(newEditor => {
            editor = newEditor;
        })
        .catch(error => {
            console.error(error);
        });

    // Sync editor content before form submit
    $('form').on('submit', function () {
        $('#editor').val(editor.getData());
    });
});
</script>
@endpush
