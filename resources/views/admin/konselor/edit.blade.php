@extends('admin.layouts.layout')

@section('content')
<section class="section">
    <div class="section-header text-black">
      <div class="section-header-back">
        <a href="{{ url('admin/konselor') }}" class="btn-back">
          <span class="btn-back__icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" height="20" width="20">
              <path d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z" fill="#fff"/>
              <path d="m237.248 512 265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z" fill="#fff"/>
            </svg>
          </span>
        </a>
      </div><br>
      <h2>Edit Konselor</h2>
    </div>
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card" style="background-color: rgb(222, 220, 240);">
            <div class="card-header">
              <h5 class="bi bi-pencil-square"> Edit Section</h5>
            </div>
            <div class="card-body">
                <form action="{{route('admin.konselor.update', $konselor->id_counselor)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3 fw-bold">NIP</label>
                      <div class="col-sm-12 col-md-7">
                        <input type="number" name="nip" class="form-control input-style" value="{{ $konselor->nip }}">
                      </div>
                    </div>

                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3 fw-bold">Foto</label>
                        <div class="col-sm-12 col-md-7">
                          <div id="image-preview" class="image-preview">
                            <img id="preview-image" src="{{ asset('storage/'.$konselor->photo) }}" alt="gambar konselor" width="150" class="mb-2">
                            <input type="file" name="photo" class="form-control input-style" id="image-upload" value="{{ $konselor->photo }}">
                          </div>
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3 fw-bold">Nama</label>
                      <div class="col-sm-12 col-md-7">
                        <input type="text" name="counselor_name" class="form-control input-style" value="{{ $konselor->counselor_name }}">
                      </div>
                    </div>

                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3 fw-bold">Menangani Kelas</label>
                        <div class="col-sm-12 col-md-7">
                          <select class="form-control input-style selectric" name="kelas">
                            <option value="{{ $konselor->kelas }}">{{ $konselor->kelas }}</option>
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII & XIII">XII & XIII</option>
                          </select>
                        </div>
                      </div>

                      <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3 fw-bold">Nomor Telepon</label>
                        <div class="col-sm-12 col-md-7">
                          <input type="number" name="contact" class="form-control input-style" value="{{ $konselor->contact }}">
                        </div>
                      </div>

                      <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3 fw-bold">Email</label>
                        <div class="col-sm-12 col-md-7">
                          <input type="email" name="email" class="form-control input-style" value="{{ $konselor->user->email }}">
                        </div>
                      </div>

                      <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3 fw-bold">Password</label>
                        <div class="col-sm-12 col-md-7">
                          <input type="password" name="password" class="form-control input-style" value="{{ $konselor->user->password }}">
                        </div>
                      </div>

                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3 fw-bold">Bio</label>
                        <div class="col-sm-12 col-md-7">
                          <textarea name="desc" class="form-control input-style h-auto" rows="3">{{ $konselor->desc }}</textarea>
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                      <div class="col-sm-12 col-md-7">
                        <button class="dt dt-btn create">Edit</button>
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
  <script>
        $('#image-upload').on('change', function () {
        const [file] = this.files;
        if (file) {
            $('#preview-image').attr('src', URL.createObjectURL(file));
        }
    });
  </script>
@endpush
