@extends('admin.layouts.master')

@section('title', 'Daftar Event')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold">Daftar Event</h1>
        <p class="text-gray-400 mt-1">Kelola semua event di platform</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.categories.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <i data-lucide="tags" class="w-5 h-5"></i> Kelola Kategori
        </a>
        <a href="{{ route('admin.events.create') }}" class="bg-[#ff2d55] hover:bg-[#ff5e3a] text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <i data-lucide="plus" class="w-5 h-5"></i> Tambah Event
        </a>
        <a href="{{ route('admin.events.export.csv') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <i data-lucide="file-text" class="w-5 h-5"></i> Export CSV
        </a>
        <a href="{{ route('admin.events.print', request()->query()) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <i data-lucide="printer" class="w-5 h-5"></i> Cetak Laporan
        </a>
    </div>
</div>

<!-- Filter dan Search -->
<div class="glass-card rounded-2xl p-5 mb-6">
    <form method="GET" action="{{ route('admin.events.index') }}" id="filterForm">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Cari Event</label>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Nama event..." class="w-full bg-black/30 border border-white/20 rounded-lg px-3 py-2 focus:outline-none focus:border-[#ff2d55] transition">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Status</label>
                <select name="status" id="statusSelect" class="w-full bg-black/30 border border-white/20 rounded-lg px-3 py-2 focus:outline-none focus:border-[#ff2d55] transition">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Urutkan</label>
                <select name="sort" id="sortSelect" class="w-full bg-black/30 border border-white/20 rounded-lg px-3 py-2 focus:outline-none focus:border-[#ff2d55] transition">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                    <option value="start_soon" {{ request('sort') == 'start_soon' ? 'selected' : '' }}>Mulai Terdekat</option>
                    <option value="start_later" {{ request('sort') == 'start_later' ? 'selected' : '' }}>Mulai Terjauh</option>
                </select>
            </div>
            <div class="flex gap-5">
                <button type="submit" class="flex items-center gap-2 bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] hover:from-[#ff1e45] hover:to-[#ff4a2a] text-white font-medium px-5 py-2.5 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.events.index') }}" class="flex items-center gap-2 bg-gray-700 hover:bg-gray-600 text-gray-200 font-medium px-5 py-2.5 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    <span>Reset</span>
                </a>
            </div>
        </div>
    </form>
</div>

<div class="glass-card rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="border-b border-white/10">
                <tr class="text-left text-gray-400">
                    <th class="p-3">#</th>
                    <th class="p-3">Event</th>
                    <th class="p-3">Organizer</th>
                    <th class="p-3">Kategori</th>
                    <th class="p-3">Tanggal Mulai</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr class="border-b border-white/5">
                    <td class="p-3">{{ $loop->iteration }}</td>
                    <td class="p-3 font-semibold">
                        {{ $event->title }}
                        @if($event->user_id == auth()->id())
                            <br><small class="text-gray-500">(Dibuat Admin)</small>
                        @endif
                    </td>
                    <td class="p-3">{{ $event->user->name ?? '-' }} </td>
                    <td class="p-3">{{ $event->category->name ?? '-' }} </td>
                    <td class="p-3">{{ $event->start_date instanceof \Carbon\Carbon ? $event->start_date->format('d M Y') : \Carbon\Carbon::parse($event->start_date)->format('d M Y') }} </td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded-full text-xs 
                            {{ $event->status == 'published' ? 'bg-green-500/20 text-green-400' : 
                               ($event->status == 'pending' ? 'bg-yellow-500/20 text-yellow-400' : 
                               ($event->status == 'rejected' ? 'bg-red-500/20 text-red-400' : 'bg-gray-500/20 text-gray-400')) }}">
                            {{ ucfirst($event->status) }}
                        </span>
                    </td>
                    <td class="p-3">
                        <div class="flex gap-2 flex-wrap">
                            @if($event->status == 'pending')
                                <form action="{{ route('admin.events.approve', $event) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-500/20 hover:bg-green-500 text-green-400 hover:text-white px-3 py-1 rounded-md text-sm transition">Approve</button>
                                </form>
                                <form action="{{ route('admin.events.reject', $event) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-yellow-500/20 hover:bg-yellow-500 text-yellow-400 hover:text-white px-3 py-1 rounded-md text-sm transition">Reject</button>
                                </form>
                            @endif
                            <a href="{{ route('admin.events.edit', $event) }}" class="bg-blue-500/20 hover:bg-blue-500 text-blue-400 hover:text-white px-3 py-1 rounded-md text-sm transition">Edit</a>
                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus? Event yang memiliki transaksi tidak bisa dihapus.')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-red-500/20 hover:bg-red-500 text-red-400 hover:text-white px-3 py-1 rounded-md text-sm transition">Hapus</button>
                            </form>
                        </div>
                    </td>
                 </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-3 text-center text-gray-500">Belum ada event</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $events->appends(request()->query())->links() }}
    </div>
</div>
@endsection