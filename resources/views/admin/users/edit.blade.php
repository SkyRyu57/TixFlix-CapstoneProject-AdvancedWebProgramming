@extends('admin.layouts.master')
@section('title', 'Edit User')
@section('content')
<div class="flex items-center mb-6"><a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-white mr-4"><i data-lucide="arrow-left" class="w-5 h-5"></i></a><h1 class="text-3xl font-bold">Edit User: {{ $user->name }}</h1></div>
<div class="glass-card rounded-2xl p-6">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">@csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div><label class="block text-sm font-medium mb-2">Nama</label><input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required></div>
            <div><label class="block text-sm font-medium mb-2">Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required></div>
            <div><label class="block text-sm font-medium mb-2">Telepon</label><input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2"></div>
            <div><label class="block text-sm font-medium mb-2">Role</label><select name="role" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2"><option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>Customer</option><option value="organizer" {{ $user->role == 'organizer' ? 'selected' : '' }}>Organizer</option><option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option></select></div>
            <div><label class="block text-sm font-medium mb-2">Status</label><select name="is_active" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2"><option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option><option value="0" {{ !$user->is_active ? 'selected' : '' }}>Nonaktif</option></select></div>
        </div>
        <div class="flex justify-end mt-8"><button type="submit" class="bg-[#ff2d55] hover:bg-[#ff5e3a] text-white px-6 py-2 rounded-lg">Simpan</button></div>
    </form>
</div>
@endsection