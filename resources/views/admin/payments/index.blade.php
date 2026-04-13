@extends('admin.layouts.master')

@section('title', 'Konfirmasi Pembayaran')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold">Konfirmasi Pembayaran</h1>
        <p class="text-gray-400 mt-1">Daftar transaksi pending yang sudah upload bukti (terlama lebih dulu)</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-6">
    @forelse($transactions as $tx)
    @php
        $event = $tx->etickets->first()?->ticket?->event;
    @endphp
    <div class="glass-card rounded-2xl p-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Info Transaksi -->
            <div class="flex-1">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-xl font-bold">{{ $event->title ?? 'Event tidak diketahui' }}</h2>
                        <p class="text-sm text-gray-400">{{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('d M Y') : '-' }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs bg-yellow-500/20 text-yellow-400">Pending</span>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div><p class="text-xs text-gray-400">Referensi</p><p class="font-mono text-sm">{{ $tx->reference_number }}</p></div>
                    <div><p class="text-xs text-gray-400">Total</p><p class="font-semibold">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</p></div>
                    <div><p class="text-xs text-gray-400">Customer</p><p class="text-sm">{{ $tx->user->name ?? '-' }}</p><p class="text-xs text-gray-500">{{ $tx->user->email ?? '-' }}</p></div>
                    <div><p class="text-xs text-gray-400">Tgl Transaksi</p><p class="text-sm">{{ $tx->created_at->format('d M Y H:i') }}</p></div>
                </div>
                @if($tx->payment_notes)
                <div class="mb-4"><p class="text-xs text-gray-400">Catatan</p><p class="text-sm bg-black/30 p-2 rounded">{{ $tx->payment_notes }}</p></div>
                @endif
                <div class="flex gap-3">
                    <form action="{{ route('admin.payments.approve', $tx) }}" method="POST">@csrf<button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Setujui</button></form>
                    <form action="{{ route('admin.payments.reject', $tx) }}" method="POST">@csrf<button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">Tolak</button></form>
                    <a href="{{ route('admin.payments.show', $tx) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Detail</a>
                </div>
            </div>
            <!-- Bukti Pembayaran (dari relasi payment) -->
            <div class="w-full lg:w-80">
                @if($tx->payment && $tx->payment->proof_image)
                <div class="bg-black/30 rounded-lg p-2 text-center">
                    <img src="{{ asset('storage/' . $tx->payment->proof_image) }}" class="max-h-48 mx-auto rounded cursor-pointer" onclick="window.open(this.src)">
                    <p class="text-xs text-gray-500 mt-2">Klik gambar untuk memperbesar</p>
                </div>
                @else
                <div class="bg-black/30 rounded-lg p-4 text-center"><i data-lucide="image" class="w-8 h-8 mx-auto mb-2"></i><p class="text-sm">Tidak ada bukti</p></div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="glass-card rounded-2xl p-6 text-center"><i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3"></i><p>Tidak ada transaksi yang perlu dikonfirmasi</p></div>
    @endforelse
</div>
<div class="mt-6">{{ $transactions->links() }}</div>
@endsection