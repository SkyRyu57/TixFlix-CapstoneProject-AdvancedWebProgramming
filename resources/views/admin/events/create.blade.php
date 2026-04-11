@extends('admin.layouts.master')

@section('title', 'Tambah Event')

@section('content')
<div class="flex items-center mb-6">
    <a href="{{ route('admin.events.index') }}" class="text-gray-400 hover:text-white mr-4">
        <i data-lucide="arrow-left" class="w-5 h-5"></i>
    </a>
    <h1 class="text-3xl font-bold">Tambah Event</h1>
</div>

<div class="glass-card rounded-2xl p-6">
    <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium mb-2">Nama Event</label>
                <input type="text" name="title" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Kategori</label>
                <select name="category_id" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
                    <option value="">Pilih</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Penyelenggara</label>
                <select name="user_id" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
                    <option value="">Pilih</option>
                    @foreach($organizers as $org)
                        <option value="{{ $org->id }}">{{ $org->name }} ({{ $org->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Status</label>
                <select name="status" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
                    <option value="pending">Pending</option>
                    <option value="published">Published</option>
                    <option value="rejected">Rejected</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Tanggal Mulai</label>
                <input type="datetime-local" name="start_date" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Tanggal Selesai</label>
                <input type="datetime-local" name="end_date" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Lokasi</label>
                <input type="text" name="location" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Deskripsi</label>
                <textarea name="description" rows="5" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Banner</label>
                <input type="file" name="banner" accept="image/*" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2">
            </div>
        </div>

        <h3 class="text-xl font-bold mt-8 mb-4">Tiket</h3>
        <div id="tickets-container">
            <div class="ticket-item grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 p-4 bg-white/5 rounded-lg">
                <input type="text" name="tickets[0][name]" placeholder="Nama Tiket" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
                <input type="number" name="tickets[0][price]" placeholder="Harga" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
                <input type="number" name="tickets[0][stock]" placeholder="Stok" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
                <input type="text" name="tickets[0][description]" placeholder="Deskripsi (opsional)" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2">
            </div>
        </div>
        <button type="button" id="add-ticket" class="text-[#ff2d55] hover:text-white text-sm flex items-center gap-1 mt-2">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Tiket
        </button>

        <div class="flex justify-end mt-8">
            <button type="submit" class="bg-[#ff2d55] hover:bg-[#ff5e3a] text-white px-6 py-2 rounded-lg transition">Simpan Event</button>
        </div>
    </form>
</div>

<script>
    let ticketIndex = 1;
    document.getElementById('add-ticket').addEventListener('click', function() {
        const container = document.getElementById('tickets-container');
        const newDiv = document.createElement('div');
        newDiv.className = 'ticket-item grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 p-4 bg-white/5 rounded-lg';
        newDiv.innerHTML = `
            <input type="text" name="tickets[${ticketIndex}][name]" placeholder="Nama Tiket" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
            <input type="number" name="tickets[${ticketIndex}][price]" placeholder="Harga" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
            <input type="number" name="tickets[${ticketIndex}][stock]" placeholder="Stok" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
            <input type="text" name="tickets[${ticketIndex}][description]" placeholder="Deskripsi (opsional)" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2">
        `;
        container.appendChild(newDiv);
        ticketIndex++;
        lucide.createIcons();
    });
</script>
@endsection