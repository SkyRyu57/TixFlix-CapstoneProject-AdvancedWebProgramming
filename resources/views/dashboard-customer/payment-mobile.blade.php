<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Konfirmasi Pembayaran - Tixflix</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background: #0b0b10; color: white; }
        .timer { font-family: monospace; font-size: 36px; font-weight: bold; letter-spacing: 5px; }
        .amount { font-size: 28px; font-weight: bold; color: #22c55e; }
    </style>
</head>
<body class="bg-[#0b0b10]">
    <div class="max-w-md mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center">
                <i class="bi bi-ticket-perforated-fill text-white text-2xl"></i>
            </div>
            <h1 class="text-xl font-bold">Tixflix</h1>
            <p class="text-gray-400 text-sm">Konfirmasi Pembayaran</p>
        </div>

        <!-- Order Info -->
        <div class="bg-white/5 rounded-2xl p-5 mb-6">
            <div class="flex justify-between mb-3">
                <span class="text-gray-400 text-sm">Order ID</span>
                <span class="font-mono text-xs">{{ $order_id }}</span>
            </div>
            <div class="flex justify-between mb-3">
                <span class="text-gray-400 text-sm">Event</span>
                <span class="font-semibold text-sm">{{ $event->title ?? 'Event' }}</span>
            </div>
            <div class="flex justify-between mb-3">
                <span class="text-gray-400 text-sm">Jumlah Tiket</span>
                <span>{{ $quantity }} tiket</span>
            </div>
            <div class="border-t border-white/10 my-3"></div>
            <div class="flex justify-between">
                <span class="text-gray-400">Total Pembayaran</span>
                <span class="amount">Rp {{ number_format($total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Timer -->
        <div class="bg-red-500/10 rounded-2xl p-5 text-center mb-6">
            <p class="text-gray-400 text-sm mb-2">Selesaikan pembayaran sebelum waktu habis!</p>
            <div class="timer text-red-400" id="timer">05:00</div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white/5 rounded-2xl p-5 mb-6">
            <h3 class="font-semibold mb-3 text-center">Metode Pembayaran</h3>
            <div class="grid grid-cols-3 gap-3">
                <div class="text-center p-3 bg-white/5 rounded-xl">
                    <i class="bi bi-qr-code text-2xl text-purple-400"></i>
                    <p class="text-xs mt-1">QRIS</p>
                </div>
                <div class="text-center p-3 bg-white/5 rounded-xl">
                    <i class="bi bi-bank2 text-2xl text-blue-400"></i>
                    <p class="text-xs mt-1">Transfer Bank</p>
                </div>
                <div class="text-center p-3 bg-white/5 rounded-xl">
                    <i class="bi bi-wallet2 text-2xl text-green-400"></i>
                    <p class="text-xs mt-1">E-Wallet</p>
                </div>
            </div>
        </div>

        <!-- Bank Account Info -->
        <div class="bg-white/5 rounded-2xl p-5 mb-6">
            <h3 class="font-semibold mb-3">Rekening Tujuan</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-2 bg-white/5 rounded-xl">
                    <div>
                        <p class="font-semibold">BCA</p>
                        <p class="text-xs text-gray-400">1234567890</p>
                        <p class="text-xs text-gray-500">a.n Tixflix</p>
                    </div>
                    <button onclick="copyToClipboard('1234567890')" class="px-3 py-1 bg-white/10 rounded-lg text-xs">
                        <i class="bi bi-copy"></i> Salin
                    </button>
                </div>
                <div class="flex justify-between items-center p-2 bg-white/5 rounded-xl">
                    <div>
                        <p class="font-semibold">Mandiri</p>
                        <p class="text-xs text-gray-400">0987654321</p>
                        <p class="text-xs text-gray-500">a.n Tixflix</p>
                    </div>
                    <button onclick="copyToClipboard('0987654321')" class="px-3 py-1 bg-white/10 rounded-lg text-xs">
                        <i class="bi bi-copy"></i> Salin
                    </button>
                </div>
            </div>
        </div>

        <!-- Confirm Button -->
        <button id="confirmPaymentBtn" class="w-full bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] py-4 rounded-xl font-bold text-white transition-all active:scale-95">
            <i class="bi bi-check-circle mr-2"></i>
            Konfirmasi Pembayaran
        </button>

        <p class="text-center text-xs text-gray-500 mt-6">
            Dengan mengklik konfirmasi, Anda menyatakan telah melakukan pembayaran sesuai total yang tertera.
        </p>
    </div>

    <script>
        let timeLeft = {{ $remaining_seconds ?? 300 }};
        let timerInterval;
        let isConfirmed = false;

        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        function startTimer() {
            const timerEl = document.getElementById('timer');
            timerInterval = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    timerEl.textContent = '00:00';
                    alert('Waktu pembayaran telah habis! Silakan pesan ulang.');
                    window.location.href = '{{ route("dashboard") }}';
                } else {
                    timeLeft--;
                    timerEl.textContent = formatTime(timeLeft);
                }
            }, 1000);
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text);
            alert('Nomor rekening telah disalin!');
        }

        async function confirmPayment() {
            if (isConfirmed) return;
            isConfirmed = true;
            
            const btn = document.getElementById('confirmPaymentBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split mr-2 animate-spin"></i>Memproses...';
            
            try {
                const response = await fetch('{{ route("payment.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        order_id: '{{ $order_id }}',
                        event_id: {{ $event->id ?? 0 }},
                        selected_tickets: '[]',
                        total_price: {{ $total_amount }}
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Pembayaran berhasil! Terima kasih.');
                    window.location.href = '{{ route("payment.page", ["order_id" => $order_id, "payment_success" => "true"]) }}';
                } else {
                    alert(result.message || 'Gagal memproses pembayaran. Silakan coba lagi.');
                    isConfirmed = false;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-circle mr-2"></i>Konfirmasi Pembayaran';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
                isConfirmed = false;
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle mr-2"></i>Konfirmasi Pembayaran';
            }
        }

        document.getElementById('confirmPaymentBtn').addEventListener('click', confirmPayment);
        startTimer();
    </script>
</body>
</html>