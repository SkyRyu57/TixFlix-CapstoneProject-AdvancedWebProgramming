<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Eventix</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b0b10; color: #ffffff; }
    </style>
</head>
<body class="antialiased overflow-x-hidden flex flex-col min-h-screen">

    <nav class="sticky top-0 z-50 flex justify-between items-center px-10 py-5 bg-[#0b0b10]/90 backdrop-blur-md border-b border-white/10">
        <div class="flex items-center space-x-3">
            <a href="{{ url('/dashboard') }}" class="text-2xl font-bold tracking-tight text-white hover:text-indigo-400 transition">Eventix</a>
        </div>
        <div class="text-gray-400 font-semibold text-sm">
            <i class="fas fa-lock mr-2 text-indigo-400"></i> Checkout Aman
        </div>
    </nav>

    <main class="flex-grow max-w-[65rem] mx-auto px-6 lg:px-10 py-10 w-full">
        
        <h1 class="text-3xl font-extrabold text-white mb-8">Selesaikan Pesanan Anda</h1>

        <div class="flex flex-col lg:flex-row gap-10">
            <div class="lg:w-2/3 space-y-6">
                
                <div class="bg-[#15151e] border border-white/5 p-6 rounded-3xl flex gap-6 items-center">
                    <img src="{{ $event->banner ? asset('storage/' . $event->banner) : 'https://images.unsplash.com/photo-1540039155733-d7696d4eb98b?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80' }}" class="w-24 h-24 rounded-2xl object-cover">
                    <div>
                        <h2 class="text-xl font-bold text-white mb-2">{{ $event->title }}</h2>
                        <p class="text-gray-400 text-sm"><i class="far fa-calendar-alt mr-2 text-indigo-400"></i> {{ \Carbon\Carbon::parse($event->start_date)->translatedFormat('d M Y') }}</p>
                        <p class="text-gray-400 text-sm mt-1"><i class="fas fa-map-marker-alt mr-2 text-indigo-400"></i> {{ $event->location }}</p>
                    </div>
                </div>

                <div class="bg-[#15151e] border border-white/5 p-8 rounded-3xl">
                    <h3 class="text-lg font-bold text-white mb-6 border-b border-white/10 pb-4">Tiket yang Dipilih</h3>
                    
                    <div class="space-y-4">
                        @foreach($selectedTickets as $item)
                        <div class="flex justify-between items-center bg-[#0b0b10] p-4 rounded-xl border border-white/5">
                            <div>
                                <h4 class="font-bold text-white">{{ $item['ticket']->name }}</h4>
                                <p class="text-gray-400 text-sm">{{ $item['quantity'] }}x Tiket</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-indigo-400">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <div class="lg:w-1/3">
                <div class="bg-[#15151e] border border-white/5 p-8 rounded-3xl sticky top-28">
                    <h3 class="text-lg font-bold text-white mb-6">Rincian Pembayaran</h3>
                    
                    <div class="space-y-4 text-sm text-gray-400 mb-6 border-b border-white/10 pb-6">
                        <div class="flex justify-between">
                            <span>Subtotal Tiket ({{ $totalTickets }} item)</span>
                            <span class="text-white">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Biaya Layanan</span>
                            <span class="text-white">Gratis</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mb-8">
                        <span class="font-bold text-gray-300">Total Bayar</span>
                        <span class="text-2xl font-extrabold text-indigo-400">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                    </div>

                    <form action="{{ url('/payment') }}" method="POST">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                        <input type="hidden" name="total_price" value="{{ $totalPrice }}">
                        
                        @foreach($selectedTickets as $item)
                            <input type="hidden" name="tickets[{{ $item['ticket']->id }}]" value="{{ $item['quantity'] }}">
                        @endforeach

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-600/30 transition">
                            Lanjut Pembayaran
                        </button>
                    </form>
                    
                    <a href="{{ url('/events/' . $event->id) }}" class="block text-center mt-4 text-sm text-gray-500 hover:text-white transition">Batal & Kembali</a>
                </div>
            </div>

        </div>
    </main>

</body>
</html>