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
        <h2 class="section-title">Edit Siswa</h2>
    </div>

    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card" style="background-color: rgb(222, 220, 240);">
            <div class="card-header">
              <h5 class="bi bi-pencil-square"> Edit Section</h5>
            </div>
            <div class="card-body">
                <form action="{{route('admin.siswa.update', $siswa->id_student)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">NIS <span class="text-danger">*</span></label>
                      <div class="col-sm-12 col-md-7">
                        <input type="text" name="nis" class="form-control input-style @error('nis') is-invalid @enderror" 
                               value="{{ old('nis', $siswa->nis) }}" required>
                        @error('nis')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>

                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Foto</label>
                        <div class="col-sm-12 col-md-7">
                          <div id="image-preview" class="image-preview">
                            <img id="preview-image" src="{{ asset('storage/'.$siswa->photo) }}" alt="gambar siswa" width="150" class="mb-2">
                            <input type="file" name="photo" class="form-control input-style @error('photo') is-invalid @enderror" 
                                   id="image-upload" accept="image/*">
                            @error('photo')
                              <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                          </div>
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nama Siswa <span class="text-danger">*</span></label>
                      <div class="col-sm-12 col-md-7">
                        <input type="text" name="student_name" class="form-control input-style @error('student_name') is-invalid @enderror" 
                               value="{{ old('student_name', $siswa->student_name) }}" required>
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
                            <option value="{{ $siswa->class_id }}">{{ $siswa->class->class_name }}</option>
                                @foreach ($kelas as $row)
                                  @if($row->id_class != $siswa->class_id)
                                    <option value="{{ $row->id_class }}" {{ old('class_id') == $row->id_class ? 'selected' : '' }}>
                                      {{ $row->class_name }}
                                    </option>
                                  @endif
                                @endforeach
                          </select>
                          @error('class_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Password</label>
                        <div class="col-sm-12 col-md-7">
                          <input type="password" name="password" class="form-control input-style @error('password') is-invalid @enderror" 
                                 placeholder="Kosongkan jika tidak ingin mengubah password">
                          <small class="form-text text-muted">Minimal 6 karakter. Kosongkan untuk tidak mengubah.</small>
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
                                       {{ old('repeat_grade', $siswa->repeat_grade) ? 'checked' : '' }}>
                                <label class="form-check-label" for="repeat_grade">
                                  <strong>🔄 Tandai Siswa Mengulang Kelas</strong>
                                </label>
                              </div>
                              <small class="text-muted d-block mt-2" style="font-size: 12px;">
                                <i class="fas fa-info-circle"></i> 
                                Centang jika siswa akan <strong>mengulang di kelas yang sama</strong> tahun depan. 
                                Siswa yang ditandai tidak akan naik kelas otomatis saat proses kenaikan tahun.
                              </small>
                              @if($siswa->repeat_grade)
                              <div class="alert alert-warning mt-2 mb-0" style="padding: 8px; font-size: 12px;">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Siswa ini saat ini ditandai untuk MENGULANG kelas.</strong>
                              </div>
                              @endif
                            </div>
                          </div>
                        </div>
                      </div>

                    <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                      <div class="col-sm-12 col-md-7">
                        <button type="submit" class="dt dt-btn create">Edit</button>
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
    $('#image-upload').on('change', function () {
        const [file] = this.files;
        if (file) {
            $('#preview-image').attr('src', URL.createObjectURL(file));
        }
    });
  </script>
@endpush