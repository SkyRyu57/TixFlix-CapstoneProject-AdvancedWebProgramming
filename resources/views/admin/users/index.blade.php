@extends('admin.layouts.master')
@section('title', 'Manajemen User')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div><h1 class="text-3xl font-bold">Manajemen User</h1><p class="text-gray-400 mt-1">Kelola semua pengguna platform</p></div>
</div>
<div class="glass-card rounded-2xl p-5 mb-6">
    <form method="GET" action="{{ route('admin.users.index') }}">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div><label class="block text-sm text-gray-400 mb-1">Cari</label><input type="text" name="search" value="{{ request('search') }}" class="w-full bg-black/30 border border-white/20 rounded-lg px-3 py-2"></div>
            <div><label class="block text-sm text-gray-400 mb-1">Role</label><select name="role" class="w-full bg-black/30 border border-white/20 rounded-lg px-3 py-2"><option value="all">Semua</option><option value="admin">Admin</option><option value="organizer">Organizer</option><option value="customer">Customer</option></select></div>
            <div><label class="block text-sm text-gray-400 mb-1">Status</label><select name="status" class="w-full bg-black/30 border border-white/20 rounded-lg px-3 py-2"><option value="all">Semua</option><option value="active">Aktif</option><option value="suspended">Nonaktif</option></select></div>
            <div class="flex gap-3"><button type="submit" class="bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] text-white px-5 py-2.5 rounded-xl">Filter</button><a href="{{ route('admin.users.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-5 py-2.5 rounded-xl">Reset</a></div>
        </div>
    </form>
</div>
<div class="glass-card rounded-2xl p-6">
    <div class="overflow-x-auto"><table class="w-full"><thead class="border-b border-white/10"><tr class="text-left text-gray-400"><th class="p-3">ID</th><th class="p-3">Nama</th><th class="p-3">Email</th><th class="p-3">Role</th><th class="p-3">Status</th><th class="p-3">Aksi</th></tr></thead><tbody>@forelse($users as $user)<tr class="border-b border-white/5"><td class="p-3">{{ $user->id }}</td><td class="p-3 font-semibold">{{ $user->name }} @if($user->id == auth()->id()) <span class="text-xs text-blue-400">(Anda)</span>@endif</td><td class="p-3">{{ $user->email }}</td><td class="p-3"><span class="px-2 py-1 rounded-full text-xs {{ $user->role == 'admin' ? 'bg-purple-500/20 text-purple-400' : ($user->role == 'organizer' ? 'bg-blue-500/20 text-blue-400' : 'bg-green-500/20 text-green-400') }}">{{ ucfirst($user->role) }}</span></td><td class="p-3"><span class="px-2 py-1 rounded-full text-xs {{ $user->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span></td><td class="p-3"><a href="{{ route('admin.users.edit', $user) }}" class="text-blue-400 hover:text-blue-300">Edit</a>@if($user->id != auth()->id())<form action="{{ route('admin.users.toggle-suspend', $user) }}" method="POST" class="inline ml-2">@csrf<button type="submit" class="text-{{ $user->is_active ? 'red' : 'green' }}-400">{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>@endif</td></tr>@empty<tr><td colspan="6" class="text-center">Tidak ada user</td></tr>@endforelse</tbody></table></div>
    <div class="mt-4">{{ $users->appends(request()->query())->links() }}</div>
</div>
@endsection