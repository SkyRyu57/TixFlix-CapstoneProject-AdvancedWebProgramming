@extends('admin.layouts.master')

@section('title', 'Detail Transaksi')

@section('content')
<div class="flex items-center mb-6">
    <a href="{{ route('admin.transactions.index') }}" class="text-gray-400 hover:text-white mr-4">
        <i data-lucide="arrow-left" class="w-5 h-5"></i>
    </a>
    <h1 class="text-3xl font-bold">Detail Transaksi</h1>
</div>

<div class="glass-card rounded-2xl p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-lg font-semibold mb-3">Informasi Transaksi</h3>
            <table class="w-full text-sm">
                <tr class="border-b border-white/10"><td class="py-2 w-1/3">Referensi</td><td>{{ $transaction->reference_number }}</td></tr>
                <tr class="border-b border-white/10"><td class="py-2">Status</td><td><span class="px-2 py-1 rounded-full text-xs bg-{{ $transaction->status == 'paid' ? 'green' : ($transaction->status == 'pending' ? 'yellow' : 'red') }}-500/20 text-{{ $transaction->status == 'paid' ? 'green' : ($transaction->status == 'pending' ? 'yellow' : 'red') }}-400">{{ ucfirst($transaction->status) }}</span></td></tr>
                <tr class="border-b border-white/10"><td class="py-2">Total Harga</td><td>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td></tr>
                <tr class="border-b border-white/10"><td class="py-2">Tanggal Transaksi</td><td>{{ $transaction->created_at->format('d M Y H:i:s') }}</td></tr>
                @if($transaction->snap_token)<tr><td class="py-2">Snap Token</td><td>{{ $transaction->snap_token }}</td></tr>@endif
            </table>
        </div>
        <div>
            <h3 class="text-lg font-semibold mb-3">Informasi Customer</h3>
            <table class="w-full text-sm">
                <tr class="border-b border-white/10"><td class="py-2 w-1/3">Nama</td><td>{{ $transaction->user->name ?? '-' }}</td></tr>
                <tr class="border-b border-white/10"><td class="py-2">Email</td><td>{{ $transaction->user->email ?? '-' }}</td></tr>
                <tr class="border-b border-white/10"><td class="py-2">Telepon</td><td>{{ $transaction->user->phone_number ?? '-' }}</td></tr>
                <tr><td class="py-2">Negara</td><td>{{ $transaction->user->country ?? '-' }}</td></tr>
            </table>
        </div>
    </div>

    <h3 class="text-lg font-semibold mt-6 mb-3">E-Tickets</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-white/10">
                <tr class="text-left text-gray-400">
                    <th class="p-2">Kode Tiket</th>
                    <th class="p-2">Event</th>
                    <th class="p-2">Tipe Tiket</th>
                    <th class="p-2">Harga</th>
                    <th class="p-2">Status Scan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaction->etickets as $et)
                <tr class="border-b border-white/5">
                    <td class="p-2">{{ $et->ticket_code }}</td>
                    <td class="p-2">{{ $et->ticket->event->title ?? '-' }}</td>
                    <td class="p-2">{{ $et->ticket->name ?? '-' }}</td>
                    <td class="p-2">Rp {{ number_format($et->ticket->price ?? 0, 0, ',', '.') }}</td>
                    <td class="p-2">@if($et->is_scanned) Sudah @else Belum @endif</td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-2 text-center text-gray-500">Tidak ada e-ticket</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transaction->status == 'pending')
    <div class="mt-6 flex gap-3 justify-end">
        <form action="{{ route('admin.transactions.update-status', $transaction) }}" method="POST">
            @csrf
            <input type="hidden" name="status" value="paid">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg">Approve & Terbitkan Tiket</button>
        </form>
        <form action="{{ route('admin.transactions.update-status', $transaction) }}" method="POST">
            @csrf
            <input type="hidden" name="status" value="failed">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg">Tolak</button>
        </form>
    </div>
    @endif
</div>
@endsection