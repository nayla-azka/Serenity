@extends('admin.layouts.layout')

@push('styles')
<style>
    body {
        background: #f5f5f5;
    }
    .profile-card {
        background: #cbdcf2;
        border-radius: 10px;
        padding: 20px;
    }
    .sidebar-img {
        width: 120px;
        height: 120px;
        background: #fff;
        border: 1px solid #ddd;
    }
    #preview-image {
        max-width: 150px;
        height: auto;
        border-radius: 6px;
    }
</style>
@endpush

@section('content')
<div class="container my-4">
    <div class="profile-card row p-4">
        <form method="POST" action="{{ route('admin.profile.update', $user->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Sidebar (photo only for konselor) -->
            <div class="col-md-3 d-flex flex-column align-items-center">
                @if($user->role === 'konselor' && $konselor)
                    <div id="image-preview" class="image-preview">
                        <img id="preview-image" src={{ asset("storage/".$konselor->photo) }} alt="gambar konselor" width="150" class="mb-2">    
                        <input type="file" name="photo" class="form-control" id="image-upload">
                    </div>
                @endif
            </div>

            <!-- Fields -->
            <div class="col-md-9">
                @if($user->role === 'konselor' && $konselor)
                    <div class="mb-3">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" 
                               value="{{ old('nip', $konselor->nip) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Konselor</label>
                        <input type="text" name="counselor_name" class="form-control" 
                               value="{{ old('counselor_name', $konselor->counselor_name) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas" class="form-control">
                            <option value="">-- Pilih Kelas --</option>
                            <option value="X" @selected($konselor->kelas=='X')>X</option>
                            <option value="XI" @selected($konselor->kelas=='XI')>XI</option>
                            <option value="XII & XIII" @selected($konselor->kelas=='XII & XIII')>XII & XIII</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kontak</label>
                        <input type="text" name="contact" class="form-control" 
                               value="{{ old('contact', $konselor->contact) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="desc" class="form-control">{{ old('desc', $konselor->desc) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="{{ old('email', $user->email) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (Kosongkan jika tidak ingin ganti)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                @else
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" 
                               value="{{ old('name', $user->name) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="{{ old('email', $user->email) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (Kosongkan jika tidak ingin ganti)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                @endif
            </div>
            <!-- Save button -->
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            </div>
        </form>

        <!-- Logout form -->
        <form method="POST" action="{{ route('admin.logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="btn btn-danger">Log Out</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
  <script>
     $('#image-upload').on('change', function () {
        const [file] = this.files;
        if (file) {
            $('#preview-image').attr('src', URL.createObjectURL(file));
        }
    });
      // fungsi toast
      function showToast(message, type = "success") {
          let toast = document.createElement("div");
          toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
          toast.role = "alert";
          toast.innerHTML = `<div class="d-flex">
              <div class="toast-body">${message}</div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>`;
          document.body.appendChild(toast);

          let bsToast = new bootstrap.Toast(toast);
          bsToast.show();

          toast.addEventListener("hidden.bs.toast", () => {
              toast.remove();
          });
      }

      // 👇 panggil berdasarkan session flash
      document.addEventListener("DOMContentLoaded", function () {
          @if(session('success'))
              showToast("{{ session('success') }}", "success");
          @endif

          @if(session('error'))
              showToast("{{ session('error') }}", "danger");
          @endif
      });
  </script>
@endpush
