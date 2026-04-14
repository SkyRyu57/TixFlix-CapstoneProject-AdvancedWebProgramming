@extends('admin.layouts.master')

@section('title', 'Tambah Event')

@section('content')
<div class="flex items-center mb-6">
    <a href="{{ route('admin.events.index') }}" class="text-gray-400 hover:text-white mr-4">
        <i data-lucide="arrow-left" class="w-5 h-5"></i>
    </a>
    <h1 class="text-3xl font-bold">Tambah Event</h1>
</div>

<!-- Tampilkan error global -->
@if ($errors->any())
<div class="bg-red-500/20 border-l-4 border-red-500 text-red-500 p-4 rounded-r-lg mb-6">
    <div class="font-bold mb-1">Terjadi kesalahan:</div>
    <ul class="list-disc list-inside text-sm">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="glass-card rounded-2xl p-6">
    <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium mb-2">Nama Event</label>
                <input type="text" name="title" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2 @error('title') border-red-500 @enderror" value="{{ old('title') }}" required>
                @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Kategori</label>
                <select name="category_id" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2 @error('category_id') border-red-500 @enderror" required>
                    <option value="">Pilih</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Penyelenggara</label>
                <select name="user_id" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2 @error('user_id') border-red-500 @enderror" required>
                    <option value="">Pilih</option>
                    @foreach($organizers as $org)
                        <option value="{{ $org->id }}" {{ old('user_id') == $org->id ? 'selected' : '' }}>{{ $org->name }} ({{ $org->email }})</option>
                    @endforeach
                </select>
                @error('user_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Status</label>
                <select name="status" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2 @error('status') border-red-500 @enderror" required>
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Tanggal Mulai</label>
                <input type="datetime-local" name="start_date" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2 @error('start_date') border-red-500 @enderror" value="{{ old('start_date') }}" required>
                @error('start_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Tanggal Selesai</label>
                <input type="datetime-local" name="end_date" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2 @error('end_date') border-red-500 @enderror" value="{{ old('end_date') }}" required>
                @error('end_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Lokasi</label>
                <input type="text" name="location" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2 @error('location') border-red-500 @enderror" value="{{ old('location') }}" required>
                @error('location') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Deskripsi</label>
                <textarea name="description" rows="5" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2 @error('description') border-red-500 @enderror" required>{{ old('description') }}</textarea>
                @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Banner</label>
                <input type="file" name="banner" accept="image/*" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2 @error('banner') border-red-500 @enderror">
                @error('banner') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <h3 class="text-xl font-bold mt-8 mb-4">Tiket</h3>
        <div id="tickets-container">
            @foreach(old('tickets', [0 => []]) as $index => $ticketData)
            <div class="ticket-item grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 p-4 bg-white/5 rounded-lg">
                <input type="text" name="tickets[{{ $index }}][name]" placeholder="Nama Tiket" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2 @error('tickets.'.$index.'.name') border-red-500 @enderror" value="{{ $ticketData['name'] ?? '' }}" required>
                <input type="number" name="tickets[{{ $index }}][price]" placeholder="Harga" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2 @error('tickets.'.$index.'.price') border-red-500 @enderror" value="{{ $ticketData['price'] ?? '' }}" required>
                <input type="number" name="tickets[{{ $index }}][stock]" placeholder="Stok" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2 @error('tickets.'.$index.'.stock') border-red-500 @enderror" value="{{ $ticketData['stock'] ?? '' }}" required>
                <input type="text" name="tickets[{{ $index }}][description]" placeholder="Deskripsi (opsional)" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2" value="{{ $ticketData['description'] ?? '' }}">
            </div>
            @endforeach
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
    let ticketIndex = {{ count(old('tickets', [0])) }};
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