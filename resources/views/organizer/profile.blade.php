@extends('organizer.layouts.master')

@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <!-- Kolom Kiri - Info Profil -->
        <div class="col-xl-4 mb-4">
            <div class="bg-dark rounded p-4 text-center shadow-sm" style="background-color: #1e293b !important;">
                <!-- Container Avatar -->
                <div class="position-relative d-inline-block mb-3">
                    <!-- Avatar bulat sempurna -->
                    <div class="rounded-circle overflow-hidden" style="width: 120px; height: 120px; border: 3px solid #f97316; background: #0f172a;">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=f97316&color=fff&size=120&rounded=true" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                        @endif
                    </div>
                    <!-- Ikon kamera -->
                    <label for="avatar-upload" class="position-absolute bottom-0 end-0 rounded-circle d-flex align-items-center justify-content-center" 
                           style="width: 34px; height: 34px; cursor: pointer; background-color: #f97316; border: 2px solid #0f172a; transition: 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"
                           title="Ubah Avatar">
                        <i class="fas fa-camera fa-sm text-white"></i>
                    </label>
                    <input type="file" id="avatar-upload" accept="image/jpeg,image/png,image/jpg" style="display: none;">
                </div>
                <h4 class="mb-1 mt-2 text-white">{{ auth()->user()->name }}</h4>
                <p class="text-light opacity-75 mb-3">{{ auth()->user()->email }}</p>
                <div class="d-flex justify-content-center gap-3 mb-3">
                    <div class="text-center">
                        <div class="fw-bold text-white">{{ auth()->user()->events_count ?? 0 }}</div>
                        <small class="text-light opacity-75">Event</small>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold text-white">{{ auth()->user()->total_sold ?? 0 }}</div>
                        <small class="text-light opacity-75">Tiket Terjual</small>
                    </div>
                </div>
                <div class="border-top border-secondary pt-3">
                    <p class="mb-1 text-white"><i class="fas fa-calendar-alt me-2"></i> Bergabung: {{ auth()->user()->created_at->translatedFormat('d F Y') }}</p>
                    <p class="mb-0 text-white"><i class="fas fa-globe me-2"></i> {{ auth()->user()->country ?? 'Indonesia' }}</p>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan - Form -->
        <div class="col-xl-8">
            <div class="bg-dark rounded p-4 shadow-sm" style="background-color: #1e293b !important;">
                <h5 class="mb-4 border-bottom border-secondary pb-2 text-white"><i class="fas fa-user-edit me-2 text-primary"></i>Pengaturan Profil</h5>
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control bg-dark text-white border-secondary" style="background-color: #0f172a !important; border-color: #334155 !important;" value="{{ old('name', auth()->user()->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white">Email</label>
                            <input type="email" name="email" class="form-control bg-dark text-white border-secondary" style="background-color: #0f172a !important; border-color: #334155 !important;" value="{{ old('email', auth()->user()->email) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white">Nomor Telepon</label>
                            <input type="text" name="phone_number" class="form-control bg-dark text-white border-secondary" style="background-color: #0f172a !important; border-color: #334155 !important;" value="{{ old('phone_number', auth()->user()->phone_number) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white">Negara</label>
                            <input type="text" name="country" class="form-control bg-dark text-white border-secondary" style="background-color: #0f172a !important; border-color: #334155 !important;" value="{{ old('country', auth()->user()->country) }}">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label text-white">Bio</label>
                            <textarea name="bio" class="form-control bg-dark text-white border-secondary" style="background-color: #0f172a !important; border-color: #334155 !important;" rows="3">{{ old('bio', auth()->user()->bio) }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label text-white">Foto Profil</label>
                            <input type="file" name="avatar" class="form-control bg-dark text-white border-secondary" style="background-color: #0f172a !important; border-color: #334155 !important;" id="avatar-file-input">
                            <small class="text-light opacity-75">Format: JPG, PNG, maks 2MB</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white">Password Baru</label>
                            <input type="password" name="new_password" class="form-control bg-dark text-white border-secondary" style="background-color: #0f172a !important; border-color: #334155 !important;">
                            <small class="text-light opacity-75">Kosongkan jika tidak ingin mengubah</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white">Konfirmasi Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control bg-dark text-white border-secondary" style="background-color: #0f172a !important; border-color: #334155 !important;">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Elemen DOM
    const avatarUploadHidden = document.getElementById('avatar-upload');
    const avatarFileInput = document.getElementById('avatar-file-input');
    const cameraLabel = document.querySelector('label[for="avatar-upload"]');
    
    // Fungsi preview avatar (local)
    function previewAvatar(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const avatarContainer = document.querySelector('.rounded-circle.overflow-hidden');
                if (avatarContainer) {
                    avatarContainer.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">`;
                }
            };
            reader.readAsDataURL(file);
        }
    }

    // Upload via AJAX (khusus dari ikon kamera)
    function uploadAvatarViaAjax(file) {
        const formData = new FormData();
        formData.append('avatar', file);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("organizer.upload-avatar") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update avatar dengan URL baru dari server (tambah timestamp agar tidak cache)
                const avatarContainer = document.querySelector('.rounded-circle.overflow-hidden');
                if (avatarContainer) {
                    avatarContainer.innerHTML = `<img src="${data.avatar_url}?t=${Date.now()}" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">`;
                }
                // Tampilkan notifikasi sukses
                alert('Avatar berhasil diupdate!');
            } else {
                alert('Gagal upload avatar');
            }
        })
        .catch(error => {
            console.error(error);
            alert('Terjadi kesalahan saat upload');
        });
    }

    // Event klik ikon kamera
    if (cameraLabel && avatarUploadHidden) {
        cameraLabel.addEventListener('click', function(e) {
            e.preventDefault();
            avatarUploadHidden.click();
        });
    }

    // Event pilih file dari ikon kamera (AJAX upload)
    if (avatarUploadHidden) {
        avatarUploadHidden.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                // Preview lokal dulu
                previewAvatar(file);
                // Upload via AJAX
                uploadAvatarViaAjax(file);
                // Kosongkan value agar bisa upload file yang sama lagi
                this.value = '';
            }
        });
    }

    // Event pilih file dari form biasa (tetap via submit form)
    if (avatarFileInput) {
        avatarFileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                previewAvatar(file);
                // Sinkron ke hidden upload (optional)
                if (avatarUploadHidden) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    avatarUploadHidden.files = dataTransfer.files;
                }
            }
        });
    }
</script>
@endsection