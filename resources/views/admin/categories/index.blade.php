@extends('admin.layouts.master')

@section('title', 'Manajemen Kategori')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold">Manajemen Kategori</h1>
        <p class="text-gray-400 mt-1">Tambah, edit, atau hapus kategori event</p>
    </div>
</div>

<!-- Form Tambah Kategori -->
<div class="glass-card rounded-2xl p-6 mb-6">
    <h2 class="text-xl font-bold mb-4">Tambah Kategori Baru</h2>
    <form action="{{ route('admin.categories.store') }}" method="POST" class="flex gap-4">
        @csrf
        <input type="text" name="name" placeholder="Nama Kategori" class="flex-1 bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
        <button type="submit" class="bg-[#ff2d55] hover:bg-[#ff5e3a] text-white px-6 py-2 rounded-lg transition">Simpan</button>
    </form>
</div>

<!-- Daftar Kategori -->
<div class="glass-card rounded-2xl p-6">
    <h2 class="text-xl font-bold mb-4">Daftar Kategori</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="border-b border-white/10">
                <tr class="text-left text-gray-400">
                    <th class="p-3">#</th>
                    <th class="p-3">Nama</th>
                    <th class="p-3">Slug</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr class="border-b border-white/5">
                    <td class="p-3">{{ $loop->iteration }}</td>
                    <td class="p-3">{{ $cat->name }}</td>
                    <td class="p-3">{{ $cat->slug }}</td>
                    <td class="p-3">
                        <form action="{{ route('admin.categories.update', $cat) }}" method="POST" class="inline-flex gap-2 items-center">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $cat->name }}" class="bg-black/30 border border-white/20 rounded-lg px-2 py-1 text-sm w-40">
                            <button type="submit" class="text-blue-400 hover:text-blue-300 text-sm">Update</button>
                        </form>
                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-3 text-center text-gray-500">Belum ada kategori</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $categories->links() }}</div>
</div>
@endsection