<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Eventix</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b0b10; color: #ffffff; }
    </style>
</head>
<body class="antialiased flex flex-col min-h-screen">

    <nav class="sticky top-0 z-50 flex justify-between items-center px-10 py-5 bg-[#0b0b10]/90 backdrop-blur-md border-b border-white/10">
        <div class="text-2xl font-bold tracking-tight text-white">Eventix</div>
        <div class="flex gap-6">
            <a href="{{ url('/dashboard') }}" class="text-gray-400 hover:text-white transition">Event</a>
            <a href="{{ route('my-tickets') }}" class="text-gray-400 hover:text-white transition">Tiket Saya</a>
            <a href="{{ url('/my-orders') }}" class="text-white font-semibold transition">Pesanan</a>
        </div>
    </nav>

    <main class="flex-grow max-w-[65rem] mx-auto px-6 lg:px-10 py-10 w-full">
        <h1 class="text-3xl font-extrabold text-white mb-8">Riwayat Pesanan</h1>

        @if($transactions->isEmpty())
            <div class="text-center bg-[#15151e] border border-white/5 p-10 rounded-3xl">
                <i class="fas fa-file-invoice-dollar text-6xl text-gray-700 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">Belum ada pesanan</h3>
                <p class="text-gray-400 mb-6">Kamu belum pernah melakukan transaksi.</p>
                <a href="{{ url('/dashboard') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-6 rounded-xl transition">Cari Event</a>
            </div>
        @else
            <div class="bg-[#15151e] rounded-2xl overflow-hidden border border-white/5">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 text-gray-400 text-sm uppercase tracking-wider">
                            <th class="p-5 border-b border-white/5">No. Referensi</th>
                            <th class="p-5 border-b border-white/5">Tanggal</th>
                            <th class="p-5 border-b border-white/5">Total Pembayaran</th>
                            <th class="p-5 border-b border-white/5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-300">
                        @foreach($transactions as $trx)
                        <tr class="hover:bg-white/5 transition border-b border-white/5 last:border-0">
                            <td class="p-5 font-mono text-white">{{ $trx->reference_number }}</td>
                            <td class="p-5">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                            <td class="p-5 font-semibold text-indigo-400">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                            <td class="p-5">
                                @if($trx->status == 'paid')
                                    <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-xs font-bold uppercase">Berhasil</span>
                                @else
                                    <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs font-bold uppercase">{{ $trx->status }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </main>

</body>
</html>