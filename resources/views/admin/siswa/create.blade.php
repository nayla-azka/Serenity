@extends('admin.layouts.layout')

@section('content')
<section class="section">
    <div class="section-header text-black">
        <div class="section-header-back">
          <a href="{{ url('admin/siswa') }}" class="btn-back">
            <span class="btn-back__icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" height="20" width="20">
                <path d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z" fill="#fff"/>
                <path d="m237.248 512 265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z" fill="#fff"/>
              </svg>
            </span>
          </a>
        </div>
        <h2 class="section-title">Tambah Siswa</h2>
    </div>
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card" style="background-color: rgb(222, 220, 240);">
            <div class="card-header">
              <h5 class="bi bi-pencil-square"> Add Section</h5>
            </div>
            <div class="card-body">
                <form action="{{route('admin.siswa.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">NIS <span class="text-danger">*</span></label>
                      <div class="col-sm-12 col-md-7">
                        <input type="text" name="nis" class="form-control input-style @error('nis') is-invalid @enderror" 
                               value="{{ old('nis') }}" required>
                        @error('nis')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>

                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Foto <span class="text-danger">*</span></label>
                        <div class="col-sm-12 col-md-7">
                            <div id="image-preview"
                                class="image-preview border rounded d-flex align-items-center justify-content-center input-style @error('photo') is-invalid @enderror"
                                style="width: 400px; height: 200px; background-size: cover; background-position: center center;">
                                <span class="text-muted">No Image</span>
                            </div>
                            <input type="file" name="photo" id="image-upload" class="form-control input-style mt-2 @error('photo') is-invalid @enderror" 
                                   accept="image/*" required />
                            @error('photo')
                              <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nama Siswa <span class="text-danger">*</span></label>
                      <div class="col-sm-12 col-md-7">
                        <input type="text" name="student_name" class="form-control input-style @error('student_name') is-invalid @enderror" 
                               value="{{ old('student_name') }}" required>
                        @error('student_name')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>

                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Kelas <span class="text-danger">*</span></label>
                        <div class="col-sm-12 col-md-7">
                          <select class="form-control input-style selectric @error('class_id') is-invalid @enderror" 
                                  name="class_id" required>
                            <option value="">Pilih Kelas</option>
                            @foreach ($kelas as $row)
                              <option value="{{ $row->id_class }}" {{ old('class_id') == $row->id_class ? 'selected' : '' }}>
                                {{ $row->class_name }}
                              </option>
                            @endforeach
                          </select>
                          @error('class_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Password <span class="text-danger">*</span></label>
                        <div class="col-sm-12 col-md-7">
                          <input type="password" name="password" class="form-control input-style @error('password') is-invalid @enderror" 
                                 required>
                          <small class="form-text text-muted">Minimal 6 karakter</small>
                          @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <!-- NEW: Repeat Grade Option -->
                      <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Status Kenaikan</label>
                        <div class="col-sm-12 col-md-7">
                          <div class="card border-warning">
                            <div class="card-body" style="padding: 15px;">
                              <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="repeat_grade" name="repeat_grade" value="1" 
                                       {{ old('repeat_grade') ? 'checked' : '' }}>
                                <label class="form-check-label" for="repeat_grade">
                                  <strong>🔄 Tandai Siswa Mengulang Kelas</strong>
                                </label>
                              </div>
                              <small class="text-muted d-block mt-2" style="font-size: 12px;">
                                <i class="fas fa-info-circle"></i> 
                                Centang jika siswa akan <strong>mengulang di kelas yang sama</strong> tahun depan. 
                                Siswa yang ditandai tidak akan naik kelas otomatis saat proses kenaikan tahun.
                              </small>
                            </div>
                          </div>
                        </div>
                      </div>

                    <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                      <div class="col-sm-12 col-md-7">
                        <button type="submit" class="dt dt-btn create">Buat</button>
                        <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">Batal</a>
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
    $(document).ready(function () {
        // listen for file input change
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
    });
  </script>
@endpush