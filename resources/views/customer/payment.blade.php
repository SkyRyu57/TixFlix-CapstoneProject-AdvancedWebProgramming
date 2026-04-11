<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran - Tixflix</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: linear-gradient(145deg, rgba(30, 30, 40, 0.8) 0%, rgba(15, 15, 20, 0.9) 100%); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3); backdrop-filter: blur(8px); }
    </style>
</head>
<body class="bg-[#0b0b0f] text-gray-100 font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[20%] left-[20%] w-[30%] h-[30%] rounded-full bg-[#ff2d55]/10 blur-[120px]"></div>
        <div class="absolute bottom-[20%] right-[20%] w-[30%] h-[30%] rounded-full bg-[#6a5af9]/10 blur-[120px]"></div>
    </div>

    <div class="w-full max-w-md glass-card rounded-3xl p-8 relative overflow-hidden">
        <a href="{{ route('dashboard') }}" class="absolute top-6 left-6 text-gray-400 hover:text-white transition-colors">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>

        <div class="text-center mt-6 mb-8">
            <h1 class="text-2xl font-extrabold mb-2">Selesaikan Pembayaran</h1>
            <p class="text-gray-400 text-sm">Scan QRIS di bawah ini menggunakan aplikasi M-Banking atau E-Wallet kamu.</p>
        </div>

        <div class="bg-white p-4 rounded-2xl w-64 h-64 mx-auto mb-6 flex items-center justify-center shadow-[0_0_30px_rgba(255,255,255,0.1)]">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=PAY-TIXFLIX-{{ rand(1000,9999) }}" alt="QRIS Code" class="w-full h-full object-contain">
        </div>

        <div class="bg-[#1e1e28]/50 border border-white/5 rounded-xl p-4 mb-8 text-center">
            <span class="text-sm text-gray-400 block mb-1">Total Tagihan</span>
            <span class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a]">
                Rp {{ number_format($totalPrice ?? 0, 0, ',', '.') }}
            </span>
        </div>

        <form action="{{ url('/payment/process') }}" method="POST">
            @csrf
            @if(isset($tickets))
                @foreach($tickets as $ticketId => $quantity)
                    @if($quantity > 0)
                        <input type="hidden" name="tickets[{{ $ticketId }}]" value="{{ $quantity }}">
                    @endif
                @endforeach
            @endif

            <button type="submit" class="w-full bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] hover:opacity-90 text-white py-4 rounded-xl font-bold text-lg shadow-lg shadow-[#ff2d55]/30 transition-all transform hover:-translate-y-1">
                Saya Sudah Bayar
            </button>
        </form>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>