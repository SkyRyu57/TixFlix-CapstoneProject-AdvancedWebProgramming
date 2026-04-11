@extends('admin.layouts.master')

@section('title', 'Edit Event')

@section('content')
<div class="flex items-center mb-6">
    <a href="{{ route('admin.events.index') }}" class="text-gray-400 hover:text-white mr-4">
        <i data-lucide="arrow-left" class="w-5 h-5"></i>
    </a>
    <h1 class="text-3xl font-bold">Edit Event: {{ $event->title }}</h1>
</div>

<div class="glass-card rounded-2xl p-6">
    <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium mb-2">Nama Event</label>
                <input type="text" name="title" value="{{ old('title', $event->title) }}" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Kategori</label>
                <select name="category_id" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $event->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Penyelenggara</label>
                <select name="user_id" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
                    @foreach($organizers as $org)
                        <option value="{{ $org->id }}" {{ $event->user_id == $org->id ? 'selected' : '' }}>{{ $org->name }} ({{ $org->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Status</label>
                <select name="status" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
                    <option value="pending" {{ $event->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="published" {{ $event->status == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="rejected" {{ $event->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ $event->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Tanggal Mulai</label>
                <input type="datetime-local" name="start_date" value="{{ \Carbon\Carbon::parse($event->start_date)->format('Y-m-d\TH:i') }}" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Tanggal Selesai</label>
                <input type="datetime-local" name="end_date" value="{{ \Carbon\Carbon::parse($event->end_date)->format('Y-m-d\TH:i') }}" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Lokasi</label>
                <input type="text" name="location" value="{{ old('location', $event->location) }}" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Deskripsi</label>
                <textarea name="description" rows="5" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>{{ old('description', $event->description) }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Banner</label>
                @if($event->banner)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $event->banner) }}" class="h-20 rounded">
                    </div>
                @endif
                <input type="file" name="banner" accept="image/*" class="w-full bg-black/30 border border-white/20 rounded-lg px-4 py-2">
            </div>
        </div>

        <h3 class="text-xl font-bold mt-8 mb-4">Tiket</h3>
        <div id="tickets-container">
            @foreach($event->tickets as $index => $ticket)
                <div class="ticket-item grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 p-4 bg-white/5 rounded-lg">
                    <input type="text" name="tickets[{{ $index }}][name]" value="{{ $ticket->name }}" placeholder="Nama Tiket" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
                    <input type="number" name="tickets[{{ $index }}][price]" value="{{ $ticket->price }}" placeholder="Harga" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
                    <input type="number" name="tickets[{{ $index }}][stock]" value="{{ $ticket->stock }}" placeholder="Stok" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2" required>
                    <input type="text" name="tickets[{{ $index }}][description]" value="{{ $ticket->description }}" placeholder="Deskripsi (opsional)" class="bg-black/30 border border-white/20 rounded-lg px-4 py-2">
                    <input type="hidden" name="tickets[{{ $index }}][id]" value="{{ $ticket->id }}">
                </div>
            @endforeach
        </div>
        <button type="button" id="add-ticket" class="text-[#ff2d55] hover:text-white text-sm flex items-center gap-1 mt-2">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Tiket
        </button>

        <div class="flex justify-end mt-8">
            <button type="submit" class="bg-[#ff2d55] hover:bg-[#ff5e3a] text-white px-6 py-2 rounded-lg transition">Update Event</button>
        </div>
    </form>
</div>

<script>
    let ticketIndex = {{ $event->tickets->count() }};
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