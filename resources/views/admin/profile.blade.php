@extends('admin.layouts.master')

@section('title', 'Profil Admin')

@section('content')
<style>
    /* Gaya khusus untuk halaman profil - tidak bergantung pada CSS luar */
    .profile-section {
        max-width: 1200px;
        margin: 0 auto;
    }
    .profile-card {
        background: #1e293b;
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid #334155;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
    }
    .profile-avatar-wrapper {
        position: relative;
        display: inline-block;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid #f97316;
        background: #0f172a;
    }
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .profile-camera {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 34px;
        height: 34px;
        background: #f97316;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 2px solid #1e293b;
        transition: transform 0.2s;
    }
    .profile-camera:hover {
        transform: scale(1.05);
    }
    .profile-camera i {
        color: white;
        font-size: 14px;
    }
    .profile-name {
        font-size: 1.25rem;
        font-weight: 600;
        color: white;
        margin-top: 0.75rem;
        margin-bottom: 0.25rem;
    }
    .profile-email {
        color: #94a3b8;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    .profile-joined {
        color: #64748b;
        font-size: 0.75rem;
    }
    .profile-form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: #f1f5f9;
        font-weight: 500;
        font-size: 0.875rem;
    }
    .profile-form-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        background: #0f172a;
        border: 1px solid #334155;
        border-radius: 0.5rem;
        color: #f1f5f9;
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .profile-form-input:focus {
        outline: none;
        border-color: #f97316;
        box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.2);
    }
    .profile-btn-save {
        background: linear-gradient(135deg, #ff2d55, #ff5e3a);
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 0.5rem;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .profile-btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 45, 85, 0.3);
    }
    .text-muted-custom {
        color: #94a3b8;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }
</style>

<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-xl-4 mb-4">
            <div class="profile-card text-center">
                <div class="profile-avatar-wrapper">
                    <div class="profile-avatar">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}?t={{ time() }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=f97316&color=fff&size=120" style="width: 100%; height: 100%; object-fit: cover;">
                        @endif
                    </div>
                    <label for="avatar-upload" class="profile-camera">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="avatar-upload" accept="image/*" style="display: none;">
                </div>
                <div class="profile-name">{{ auth()->user()->name }}</div>
                <div class="profile-email">{{ auth()->user()->email }}</div>
                <div class="profile-joined">Bergabung: {{ auth()->user()->created_at->format('d M Y') }}</div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="profile-card">
                <h5 class="mb-4 text-white">Edit Profil</h5>
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="profile-form-label">Nama</label>
                        <input type="text" name="name" class="profile-form-input" value="{{ old('name', auth()->user()->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="profile-form-label">Email</label>
                        <input type="email" name="email" class="profile-form-input" value="{{ old('email', auth()->user()->email) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="profile-form-label">Telepon</label>
                        <input type="text" name="phone_number" class="profile-form-input" value="{{ old('phone_number', auth()->user()->phone_number) }}">
                    </div>
                    <div class="mb-3">
                        <label class="profile-form-label">Bio</label>
                        <textarea name="bio" class="profile-form-input" rows="3">{{ old('bio', auth()->user()->bio) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="profile-form-label">Avatar</label>
                        <input type="file" name="avatar" class="profile-form-input" id="avatar-file-input">
                        <div class="text-muted-custom">Format: JPG, PNG, maks 2MB</div>
                    </div>
                    <div class="mb-3">
                        <label class="profile-form-label">Password Baru</label>
                        <input type="password" name="new_password" class="profile-form-input">
                        <div class="text-muted-custom">Kosongkan jika tidak ingin mengubah</div>
                    </div>
                    <div class="mb-3">
                        <label class="profile-form-label">Konfirmasi Password</label>
                        <input type="password" name="new_password_confirmation" class="profile-form-input">
                    </div>
                    <button type="submit" class="profile-btn-save">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const avatarUpload = document.getElementById('avatar-upload');
        const avatarFileInput = document.getElementById('avatar-file-input');
        const cameraLabel = document.querySelector('.profile-camera');
        const avatarImg = document.querySelector('.profile-avatar img');

        function previewAvatar(file) {
            if (file && avatarImg) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        if (cameraLabel && avatarUpload) {
            cameraLabel.addEventListener('click', function(e) {
                e.preventDefault();
                avatarUpload.click();
            });
        }

        if (avatarUpload) {
            avatarUpload.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    previewAvatar(e.target.files[0]);
                    if (avatarFileInput) {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(e.target.files[0]);
                        avatarFileInput.files = dataTransfer.files;
                    }
                }
            });
        }

        if (avatarFileInput) {
            avatarFileInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    previewAvatar(e.target.files[0]);
                    if (avatarUpload) {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(e.target.files[0]);
                        avatarUpload.files = dataTransfer.files;
                    }
                }
            });
        }
    });
</script>
@endsection