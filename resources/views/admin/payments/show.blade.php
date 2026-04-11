@extends('admin.layouts.master')
@section('title', 'Detail Konfirmasi')
@section('content')
<div class="flex items-center mb-6"><a href="{{ route('admin.payments.index') }}" class="text-gray-400 hover:text-white mr-4"><i data-lucide="arrow-left" class="w-5 h-5"></i></a><h1 class="text-3xl font-bold">Detail Konfirmasi</h1></div>
<div class="glass-card rounded-2xl p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div><h3 class="text-lg font-semibold mb-3">Informasi Transaksi</h3><table class="w-full text-sm"><tr><td class="py-2 w-1/3">Referensi</td><td>{{ $transaction->reference_number }}</td></tr><tr><td class="py-2">Total</td><td>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td></tr><tr><td class="py-2">Status</td><td><span class="px-2 py-1 rounded-full text-xs bg-yellow-500/20 text-yellow-400">Pending</span></td></tr><tr><td class="py-2">Catatan</td><td>{{ $transaction->payment_notes ?? '-' }}</td></tr></table></div>
        <div><h3 class="text-lg font-semibold mb-3">Customer</h3><table class="w-full text-sm"><tr><td class="py-2 w-1/3">Nama</td><td>{{ $transaction->user->name ?? '-' }}</td></tr><tr><td class="py-2">Email</td><td>{{ $transaction->user->email ?? '-' }}</td></tr><tr><td class="py-2">Telepon</td><td>{{ $transaction->user->phone_number ?? '-' }}</td></tr></table></div>
    </div>
    @if($transaction->payment_proof)
    <div class="mt-6"><h3 class="text-lg font-semibold mb-3">Bukti Pembayaran</h3><div class="bg-black/30 rounded-lg p-4 text-center"><img src="{{ asset('storage/' . $transaction->payment_proof) }}" class="max-h-96 mx-auto rounded"></div></div>
    @endif
    <div class="mt-6 flex justify-end gap-3">
        <form action="{{ route('admin.payments.approve', $transaction) }}" method="POST">@csrf<button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg">Setujui</button></form>
        <form action="{{ route('admin.payments.reject', $transaction) }}" method="POST">@csrf<button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg">Tolak</button></form>
    </div>
</div>
@endsection