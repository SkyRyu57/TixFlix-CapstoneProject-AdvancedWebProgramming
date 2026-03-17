<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} - Eventix</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b0b10; color: #ffffff; }
        /* Menghilangkan panah atas/bawah bawaan input type number */
        input[type="number"]::-webkit-inner-spin-button, input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type="number"] { -moz-appearance: textfield; }
    </style>
</head>
<body class="antialiased overflow-x-hidden flex flex-col min-h-screen">

    <nav class="sticky top-0 z-50 flex justify-between items-center px-10 py-5 bg-[#0b0b10]/90 backdrop-blur-md border-b border-white/10">
        <div class="flex items-center space-x-3">
            <div class="bg-indigo-500 text-white p-2 rounded-lg shadow-lg shadow-indigo-500/30"><i class="fas fa-ticket-alt"></i></div>
            <a href="{{ url('/dashboard') }}" class="text-2xl font-bold tracking-tight hover:text-indigo-400 transition">Eventix</a>
        </div>
        <div class="hidden md:flex space-x-8 text-sm font-semibold text-gray-400">
            <a href="{{ url('/dashboard') }}" class="hover:text-white transition">Beranda</a>
            <a href="{{ url('/dashboard#semua-event') }}" class="text-white bg-white/10 px-4 py-2 rounded-full border border-white/5">Event</a>
            <a href="{{ url('/dashboard#tiket-saya') }}" class="hover:text-white transition">Tiket Saya</a>
        </div>
        <div class="flex items-center space-x-6 text-gray-400">
            <a href="{{ url('/dashboard') }}" class="hover:text-white transition text-sm font-semibold"><i class="fas fa-home mr-2"></i>Kembali ke Dashboard</a>
        </div>
    </nav>

    <main class="flex-grow max-w-[85rem] mx-auto px-6 lg:px-10 py-10 w-full">
        
        <a href="{{ url('/dashboard') }}" class="inline-flex items-center text-gray-400 hover:text-white transition mb-6 font-medium text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>

        <div class="w-full h-[300px] md:h-[450px] rounded-3xl overflow-hidden mb-10 relative border border-white/5">
            <img src="{{ $event->banner ? asset('storage/' . $event->banner) : 'https://images.unsplash.com/photo-1540039155733-d7696d4eb98b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80' }}" alt="Banner {{ $event->title }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-[#0b0b10] via-transparent to-transparent opacity-80"></div>
        </div>

        <div class="flex flex-col lg:flex-row gap-12">
            
            <div class="lg:w-2/3">
                <span class="inline-block bg-indigo-500/20 text-indigo-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 border border-indigo-500/20">
                    {{ $event->category->name ?? 'Event' }}
                </span>
                <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-2 leading-tight">{{ $event->title }}</h1>
                <p class="text-gray-500 text-sm mb-8">Oleh <span class="text-white font-semibold">Eventix Organizer</span></p> 
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                    <div class="bg-[#15151e] border border-white/5 p-5 rounded-2xl flex items-start space-x-4">
                        <div class="bg-indigo-500/10 text-indigo-400 p-3 rounded-xl"><i class="far fa-calendar-alt text-xl"></i></div>
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">Tanggal</p>
                            <p class="text-white font-bold text-sm">{{ \Carbon\Carbon::parse($event->start_date)->translatedFormat('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="bg-[#15151e] border border-white/5 p-5 rounded-2xl flex items-start space-x-4">
                        <div class="bg-purple-500/10 text-purple-400 p-3 rounded-xl"><i class="far fa-clock text-xl"></i></div>
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">Waktu</p>
                            <p class="text-white font-bold text-sm">
                                {{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }} - 
                                {{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('H:i') : 'Selesai' }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-[#15151e] border border-white/5 p-5 rounded-2xl flex items-start space-x-4">
                        <div class="bg-pink-500/10 text-pink-400 p-3 rounded-xl"><i class="fas fa-map-marker-alt text-xl"></i></div>
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">Lokasi</p>
                            <p class="text-white font-bold text-sm line-clamp-2">{{ $event->location }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#15151e] border border-white/5 p-8 rounded-3xl mb-8">
                    <h3 class="text-xl font-bold text-white mb-4">Tentang Event</h3>
                    <div class="text-gray-400 text-sm leading-relaxed whitespace-pre-line">
                        {{ $event->description }}
                    </div>
                </div>

                <div class="bg-[#15151e] border border-white/5 p-8 rounded-3xl">
                    <h3 class="text-xl font-bold text-white mb-4">Alamat</h3>
                    <p class="text-gray-400 text-sm"><i class="fas fa-map-pin mr-2 text-indigo-400"></i> {{ $event->location }}</p>
                </div>
            </div>

            <div class="lg:w-1/3">
                <h3 class="text-xl font-bold text-white mb-6">Pilih Tiket</h3>

                <form action="{{ url('/checkout') }}" method="POST" id="ticketForm">
                    @csrf
                    <input type="hidden" name="event_id" value="{{ $event->id }}">

                    @forelse($event->tickets as $ticket)
                    <div class="bg-[#15151e] border border-white/10 p-6 rounded-3xl mb-5 {{ $ticket->stock == 0 ? 'opacity-50' : 'hover:border-indigo-500/50' }} transition duration-300">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="text-lg font-bold text-white">{{ $ticket->name }}</h4>
                            <div class="text-right">
                                @if($ticket->stock == 0)
                                    <span class="bg-red-500/20 text-red-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Sold Out</span>
                                    <p class="text-gray-500 font-bold text-lg line-through decoration-red-500">Rp {{ number_format($ticket->price, 0, ',', '.') }}</p>
                                @else
                                    <p class="text-indigo-400 font-bold text-lg">Rp {{ number_format($ticket->price, 0, ',', '.') }}</p>
                                    <p class="text-gray-500 text-[10px]">{{ $ticket->stock }} tersisa</p>
                                @endif
                            </div>
                        </div>
                        
                        <p class="text-gray-400 text-xs mb-6">{{ $ticket->description }}</p>
                        
                        @if($ticket->stock > 0)
                        <div class="flex items-center space-x-4 bg-[#0b0b10] rounded-xl p-1 w-fit border border-white/5">
                            <button type="button" onclick="updateQty('ticket-{{ $ticket->id }}', -1)" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition"><i class="fas fa-minus"></i></button>
                            
                            <input type="number" 
                                   name="tickets[{{ $ticket->id }}]" 
                                   id="ticket-{{ $ticket->id }}" 
                                   value="0" 
                                   min="0" 
                                   max="{{ $ticket->stock }}" 
                                   class="font-bold text-white w-6 text-center bg-transparent border-none focus:ring-0 p-0" 
                                   readonly>
                            
                            <button type="button" onclick="updateQty('ticket-{{ $ticket->id }}', 1, {{ $ticket->stock }})" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition"><i class="fas fa-plus"></i></button>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="bg-[#15151e] border border-white/10 p-6 rounded-3xl mb-5 text-center">
                        <p class="text-gray-400 text-sm">Belum ada tiket yang tersedia untuk event ini.</p>
                    </div>
                    @endforelse

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 rounded-xl mt-6 shadow-lg shadow-indigo-600/30 transition">
                        Beli Tiket
                    </button>
                </form>
                </div>
        </div>
    </main>

    <footer class="border-t border-white/10 mt-10 py-10 text-center text-gray-500 text-sm">
        <p>&copy; 2026 Eventix by TixFlix. All rights reserved.</p>
    </footer>

    <script>
        function updateQty(inputId, change, maxStock = 9999) {
            let input = document.getElementById(inputId);
            let currentValue = parseInt(input.value) || 0;
            let newValue = currentValue + change;

            // Pastikan jumlah tiket tidak kurang dari 0 dan tidak lebih dari stok
            if (newValue >= 0 && newValue <= maxStock) {
                input.value = newValue;
            }
        }

        // Opsional: Validasi form supaya user gak bisa klik "Beli Tiket" kalau belum pilih satupun
        document.getElementById('ticketForm').addEventListener('submit', function(e) {
            let inputs = document.querySelectorAll('input[type="number"]');
            let totalTickets = 0;
            inputs.forEach(input => {
                totalTickets += parseInt(input.value) || 0;
            });

            if (totalTickets === 0) {
                e.preventDefault(); // Cegah form terkirim
                alert('Silakan pilih minimal 1 tiket terlebih dahulu!');
            }
        });
    </script>
</body>
</html>