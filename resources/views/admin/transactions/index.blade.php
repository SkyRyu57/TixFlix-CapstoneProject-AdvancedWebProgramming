@extends('admin.layouts.master')

@section('title', 'Daftar Transaksi')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold">Daftar Transaksi</h1>
        <p class="text-gray-400 mt-1">Kelola semua transaksi pembayaran</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.transactions.export.csv', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <i data-lucide="file-text" class="w-5 h-5"></i> Export CSV
        </a>
    </div>
</div>

<!-- Filter -->
<div class="glass-card rounded-2xl p-5 mb-6">
    <form method="GET" action="{{ route('admin.transactions.index') }}">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Cari (Ref/Customer)</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Referensi atau nama..." class="w-full bg-black/30 border border-white/20 rounded-lg px-3 py-2 focus:outline-none focus:border-[#ff2d55] transition">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Status</label>
                <select name="status" class="w-full bg-black/30 border border-white/20 rounded-lg px-3 py-2 focus:outline-none focus:border-[#ff2d55] transition">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Urutkan</label>
                <select name="sort" class="w-full bg-black/30 border border-white/20 rounded-lg px-3 py-2 focus:outline-none focus:border-[#ff2d55] transition">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                    <option value="amount_high" {{ request('sort') == 'amount_high' ? 'selected' : '' }}>Nominal Tertinggi</option>
                    <option value="amount_low" {{ request('sort') == 'amount_low' ? 'selected' : '' }}>Nominal Terendah</option>
                </select>
            </div>
            <div class="flex gap-3 items-center">
                <button type="submit" class="flex items-center gap-2 bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] hover:from-[#ff1e45] hover:to-[#ff4a2a] text-white font-medium px-5 py-2.5 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="search" class="w-4 h-6"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.transactions.index') }}" class="flex items-center gap-2 bg-gray-700 hover:bg-gray-600 text-gray-200 font-medium px-5 py-2.5 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="x" class="w-4 h-6"></i>
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
                    <th class="p-3">ID</th>
                    <th class="p-3">Referensi</th>
                    <th class="p-3">Customer</th>
                    <th class="p-3">Total</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Tanggal</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr class="border-b border-white/5">
                    <td class="p-3">{{ $tx->id }}</td>
                    <td class="p-3 font-mono text-sm">{{ $tx->reference_number }}</td>
                    <td class="p-3">{{ $tx->user->name ?? 'N/A' }}<br><small class="text-gray-500">{{ $tx->user->email ?? '' }}</small></td>
                    <td class="p-3">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded-full text-xs 
                            {{ $tx->status == 'paid' ? 'bg-green-500/20 text-green-400' : 
                               ($tx->status == 'pending' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-red-500/20 text-red-400') }}">
                            {{ ucfirst($tx->status) }}
                        </span>
                    </td>
                    <td class="p-3">{{ $tx->created_at->format('d M Y H:i') }}</td>
                    <td class="p-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.transactions.show', $tx) }}" class="text-blue-400 hover:text-blue-300">Detail</a>
                            @if($tx->status == 'pending')
                                <form action="{{ route('admin.transactions.update-status', $tx) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="paid">
                                    <button type="submit" class="text-green-400 hover:text-green-300">Approve</button>
                                </form>
                                <form action="{{ route('admin.transactions.update-status', $tx) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="failed">
                                    <button type="submit" class="text-red-400 hover:text-red-300">Reject</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-3 text-center text-gray-500">Tidak ada transaksi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $transactions->appends(request()->query())->links() }}</div>
</div>
@endsection