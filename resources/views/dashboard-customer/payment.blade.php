<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Tixflix</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b0b10; color: #ffffff; }
        .glass-panel { background: rgba(18, 18, 24, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-card { background: linear-gradient(145deg, rgba(30, 30, 40, 0.8) 0%, rgba(15, 15, 20, 0.9) 100%); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3); backdrop-filter: blur(8px); }
        .text-gradient { background: linear-gradient(135deg, #ff2d55 0%, #ff5e3a 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        
        .timer {
            font-family: 'Courier New', monospace;
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 3px;
        }
        
        .bank-card {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s;
        }
        .bank-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255,45,85,0.5);
        }
        
        .upload-area {
            border: 2px dashed rgba(255,255,255,0.2);
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .upload-area:hover {
            border-color: #ff2d55;
            background: rgba(255,45,85,0.05);
        }
        .upload-area.dragover {
            border-color: #ff2d55;
            background: rgba(255,45,85,0.1);
        }
        
        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 12px;
            margin-top: 12px;
        }
        
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: linear-gradient(145deg, rgba(30, 30, 40, 0.95) 0%, rgba(15, 15, 20, 0.98) 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 32px;
            max-width: 400px;
            width: 90%;
            text-align: center;
        }
    </style>
</head>
<body class="bg-[#0b0b0f] text-gray-100">

    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-[#ff2d55]/10 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] rounded-full bg-[#6a5af9]/10 blur-[120px]"></div>
    </div>

    <nav class="sticky top-0 z-50 glass-panel border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3 cursor-pointer group" onclick="window.location.href='{{ route('dashboard') }}'">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center">
                        <i data-lucide="ticket" class="w-6 h-6 text-white transform -rotate-12"></i>
                    </div>
                    <span class="text-xl font-bold">Tix<span class="text-gradient">flix</span></span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center font-bold">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-12">
        <div class="glass-card rounded-3xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold mb-2">Pembayaran Tiket</h1>
                <p class="text-gray-400">Silakan transfer ke rekening berikut dan upload bukti pembayaran</p>
                <p class="text-gray-500 text-sm mt-2">Sisa waktu: <span id="timer" class="text-[#ff2d55] font-bold text-xl">05:00</span></p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Left: Order Summary -->
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold mb-4">Detail Pesanan</h2>
                    
                    <div class="bg-white/5 rounded-xl p-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Order ID</span>
                            <span class="font-mono text-sm" id="orderId">{{ $orderId ?? 'ORD-' . uniqid() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Event</span>
                            <span class="font-semibold">{{ $event->title ?? 'Event' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Tanggal</span>
                            <span>{{ isset($event) ? \Carbon\Carbon::parse($event->start_date)->format('d M Y H:i') : '-' }} WIB</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Lokasi</span>
                            <span>{{ $event->location ?? '-' }}</span>
                        </div>
                        <div class="border-t border-white/10 my-2"></div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Jumlah Tiket</span>
                            <span class="font-semibold">{{ $totalTickets ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Total Pembayaran</span>
                            <span class="text-2xl font-bold text-green-400">Rp {{ number_format($totalPrice ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Detail Tiket -->
                    @if(isset($selectedTickets) && count($selectedTickets) > 0)
                    <div class="bg-white/5 rounded-xl p-4">
                        <h3 class="font-semibold mb-2">Detail Tiket:</h3>
                        @foreach($selectedTickets as $item)
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ $item['ticket']->name }} x{{ $item['quantity'] }}</span>
                                <span>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Right: Bank Account & Upload Proof -->
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold mb-4">Informasi Pembayaran</h2>
                    
                    <!-- Bank Accounts -->
                    <div class="bank-card">
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-bank2 text-blue-400 text-xl"></i>
                                <span class="font-bold">Bank Central Asia (BCA)</span>
                            </div>
                            <button onclick="copyToClipboard('1234567890')" class="text-xs bg-white/10 hover:bg-white/20 px-2 py-1 rounded-lg transition-colors">
                                <i class="bi bi-copy"></i> Salin
                            </button>
                        </div>
                        <p class="text-lg font-mono">1234567890</p>
                        <p class="text-sm text-gray-400">a.n Tixflix Event Organizer</p>
                    </div>
                    
                    <div class="bank-card">
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-bank2 text-purple-400 text-xl"></i>
                                <span class="font-bold">Bank Mandiri</span>
                            </div>
                            <button onclick="copyToClipboard('0987654321')" class="text-xs bg-white/10 hover:bg-white/20 px-2 py-1 rounded-lg transition-colors">
                                <i class="bi bi-copy"></i> Salin
                            </button>
                        </div>
                        <p class="text-lg font-mono">0987654321</p>
                        <p class="text-sm text-gray-400">a.n Tixflix Event Organizer</p>
                    </div>
                    
                    <!-- E-Wallet -->
                    <div class="bank-card">
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-wallet2 text-green-400 text-xl"></i>
                                <span class="font-bold">DANA / OVO / GoPay</span>
                            </div>
                            <button onclick="copyToClipboard('081234567890')" class="text-xs bg-white/10 hover:bg-white/20 px-2 py-1 rounded-lg transition-colors">
                                <i class="bi bi-copy"></i> Salin
                            </button>
                        </div>
                        <p class="text-lg font-mono">0812-3456-7890</p>
                        <p class="text-sm text-gray-400">a.n Tixflix Official</p>
                    </div>
                    
                    <!-- Upload Bukti Transfer -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium mb-2">Upload Bukti Transfer</label>
                        
                        <form id="paymentForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $orderId }}">
                            <input type="hidden" name="amount" value="{{ $totalPrice }}">
                            
                            <div id="uploadArea" class="upload-area">
                                <i data-lucide="upload-cloud" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                                <p class="text-gray-400">Klik atau drag & drop file di sini</p>
                                <p class="text-gray-500 text-xs mt-1">Format: JPG, PNG, PDF (Max 2MB)</p>
                                <input type="file" id="proofImage" name="proof_image" accept="image/*,application/pdf" class="hidden" required>
                            </div>
                            
                            <div id="imagePreview" class="mt-4 text-center hidden">
                                <img id="previewImg" class="preview-image" alt="Preview">
                                <button type="button" onclick="removeImage()" class="text-xs text-red-400 mt-2">Hapus</button>
                            </div>
                            
                            <div class="mt-4">
                                <label class="block text-sm font-medium mb-2">Catatan (Opsional)</label>
                                <textarea name="notes" rows="2" class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors" placeholder="Contoh: Transfer dari BCA a.n Budi"></textarea>
                            </div>
                            
                            <button type="submit" id="submitPaymentBtn" class="w-full mt-4 bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] py-3 rounded-xl font-bold hover:opacity-90 transition-all">
                                <i data-lucide="send" class="w-4 h-4 inline mr-2"></i>
                                Konfirmasi Pembayaran
                            </button>
                        </form>
                    </div>
                    
                    <div class="bg-yellow-500/10 rounded-xl p-3 text-xs text-yellow-400 mt-4">
                        <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                        Setelah mengupload bukti transfer, admin akan memverifikasi pembayaran Anda. Status dapat dilihat di halaman My Tickets.
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Success Modal -->
    <div id="successModal" class="modal-overlay">
        <div class="modal-content">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-green-500/20 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-10 h-10 text-green-400"></i>
            </div>
            <h3 class="text-2xl font-bold mb-2">Pembayaran Dikonfirmasi!</h3>
            <p class="text-gray-400 mb-6">Bukti pembayaran Anda telah terkirim. Silakan tunggu verifikasi dari admin.</p>
            <button onclick="window.location.href='{{ route('my-tickets') }}'" class="w-full bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] py-3 rounded-xl font-bold">
                Lihat Tiket Saya
            </button>
        </div>
    </div>

    <!-- Expired Modal -->
    <div id="expiredModal" class="modal-overlay">
        <div class="modal-content">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-red-500/20 flex items-center justify-center">
                <i data-lucide="alert-circle" class="w-10 h-10 text-red-400"></i>
            </div>
            <h3 class="text-2xl font-bold mb-2">Waktu Habis!</h3>
            <p class="text-gray-400 mb-6">Waktu pembayaran telah habis. Silakan pesan ulang tiket Anda.</p>
            <button onclick="window.location.href='{{ route('dashboard') }}'" class="w-full bg-white/10 hover:bg-white/20 py-3 rounded-xl font-bold transition-all">
                Kembali ke Beranda
            </button>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const orderId = document.getElementById('orderId').innerText;
        
        // Timer 5 menit (300 detik)
        let timeLeft = 300;
        let timerInterval;

        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        function startTimer() {
            timerInterval = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById('timer').textContent = '00:00';
                    showExpiredModal();
                } else {
                    timeLeft--;
                    document.getElementById('timer').textContent = formatTime(timeLeft);
                }
            }, 1000);
        }

        function showExpiredModal() {
            document.getElementById('expiredModal').style.display = 'flex';
        }

        // Copy to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text);
            alert('Nomor rekening telah disalin!');
        }

        // Upload area
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('proofImage');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        
        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                fileInput.files = e.dataTransfer.files;
                previewFile(file);
            }
        });
        
        fileInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files[0]) {
                previewFile(e.target.files[0]);
            }
        });
        
        function previewFile(file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                previewImg.src = event.target.result;
                imagePreview.classList.remove('hidden');
                uploadArea.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
        
        function removeImage() {
            fileInput.value = '';
            imagePreview.classList.add('hidden');
            uploadArea.style.display = 'block';
        }
        
        // Submit payment
        const paymentForm = document.getElementById('paymentForm');
        const submitBtn = document.getElementById('submitPaymentBtn');
        
        paymentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!fileInput.files[0]) {
                alert('Silakan upload bukti pembayaran terlebih dahulu!');
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split mr-2 animate-spin"></i>Mengirim...';
            
            const formData = new FormData(paymentForm);
            
            try {
                const response = await fetch('{{ route("payment.confirm") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    clearInterval(timerInterval);
                    document.getElementById('successModal').style.display = 'flex';
                } else {
                    alert(result.message || 'Gagal mengirim bukti pembayaran. Silakan coba lagi.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i data-lucide="send" class="w-4 h-4 inline mr-2"></i>Konfirmasi Pembayaran';
                    lucide.createIcons();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i data-lucide="send" class="w-4 h-4 inline mr-2"></i>Konfirmasi Pembayaran';
                lucide.createIcons();
            }
        });
        
        startTimer();
    </script>
</body>
</html>