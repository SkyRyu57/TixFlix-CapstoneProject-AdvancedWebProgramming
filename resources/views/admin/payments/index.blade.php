@extends('admin.layouts.master')

@section('title', 'Konfirmasi Pembayaran')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold">Konfirmasi Pembayaran</h1>
        <p class="text-gray-400 mt-1">Verifikasi bukti pembayaran dari customer</p>
    </div>
</div>

<div class="glass-card rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="border-b border-white/10">
                <tr class="text-left text-gray-400">
                    <th class="p-3">ID</th>
                    <th class="p-3">Order ID</th>
                    <th class="p-3">Customer</th>
                    <th class="p-3">Jumlah</th>
                    <th class="p-3">Bukti</th>
                    <th class="p-3">Tanggal</th>
                    <th class="p-3">Aksi</th>
                 </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr class="border-b border-white/5">
                    <td class="p-3">{{ $payment->id }}</td>
                    <td class="p-3 font-mono text-sm">{{ $payment->order_id }}</td>
                    <td class="p-3">
                        {{ $payment->user->name ?? '-' }}<br>
                        <small class="text-gray-500">{{ $payment->user->email ?? '-' }}</small>
                    </td>
                    <td class="p-3">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td class="p-3">
                        <a href="{{ Storage::url($payment->proof_image) }}" target="_blank" class="text-blue-400 hover:text-blue-300">
                            <i data-lucide="eye" class="w-5 h-5 inline"></i> Lihat
                        </a>
                    </td>
                    <td class="p-3">{{ $payment->created_at->format('d M Y H:i') }}</td>
                    <td class="p-3">
                        <div class="flex gap-2">
                            <form action="{{ route('admin.payments.approve', $payment->transaction_id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-500/20 hover:bg-green-500 text-green-400 hover:text-white px-3 py-1 rounded-md text-sm transition">
                                    Verifikasi
                                </button>
                            </form>
                            <form action="{{ route('admin.payments.reject', $payment->transaction_id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-red-500/20 hover:bg-red-500 text-red-400 hover:text-white px-3 py-1 rounded-md text-sm transition">
                                    Tolak
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-3 text-center text-gray-500">Tidak ada pembayaran yang menunggu konfirmasi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $payments->links() }}
    </div>
</div>
@endsection