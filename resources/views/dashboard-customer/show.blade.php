<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->title }} - Tixflix</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b0b10; color: #ffffff; }
        
        .glass-panel {
            background: rgba(18, 18, 24, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .glass-card {
            background: linear-gradient(145deg, rgba(30, 30, 40, 0.8) 0%, rgba(15, 15, 20, 0.9) 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #ff2d55 0%, #ff5e3a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#0b0b0f] text-gray-100 font-sans antialiased min-h-screen selection:bg-[#ff2d55] selection:text-white pb-20">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-[#ff2d55]/10 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] rounded-full bg-[#6a5af9]/10 blur-[120px]"></div>
    </div>

    <!-- NAVBAR SAMA SEPERTI DASHBOARD CUSTOMER -->
    <nav class="sticky top-0 z-50 glass-panel border-b border-white/10 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3 cursor-pointer group" onclick="window.location.href='{{ route('dashboard') }}'">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center shadow-lg shadow-[#ff2d55]/30 group-hover:scale-105 transition-transform duration-300">
                        <i data-lucide="ticket" class="w-6 h-6 text-white transform -rotate-12"></i>
                    </div>
                    <span class="text-xl md:text-2xl font-bold tracking-tight">Tix<span class="text-gradient">flix</span></span>
                </div>

                <div class="hidden md:flex space-x-8 items-center">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white font-medium transition-colors">
                        Home
                    </a>
                    <a href="{{ route('concerts') }}" class="text-gray-400 hover:text-white font-medium transition-colors">
                        Concerts
                    </a>
                    <a href="{{ route('festivals') }}" class="text-gray-400 hover:text-white font-medium transition-colors">
                        Festivals
                    </a>
                    <a href="{{ route('my-tickets') }}" class="text-gray-400 hover:text-white font-medium transition-colors">
                        My Tickets
                    </a>
                </div>

                <div class="flex items-center gap-2 md:gap-4">
                    <button class="p-2 text-gray-400 hover:text-white transition-colors relative group hidden sm:block">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-[#ff2d55] rounded-full border border-[#0b0b0f]"></span>
                    </button>
                    
                    <div class="relative flex items-center gap-3 md:pl-4 md:border-l border-white/10">
                        <div class="hidden md:block text-right">
                            <div class="text-sm font-semibold">{{ auth()->user()->name ?? 'Guest User' }}</div>
                            <div class="text-xs text-gray-400">Event Explorer</div>
                        </div>
                        <div class="w-10 h-10 rounded-full border-2 border-[#1e1e28] bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center text-white font-bold hover:border-[#ff2d55] transition-colors cursor-pointer shadow-lg shadow-[#ff2d55]/20">
                            {{ substr(auth()->user()->name ?? 'G', 0, 1) }}
                        </div>
                        
                        <form action="{{ route('logout') }}" method="POST" class="ml-2">
                            @csrf
                            <button type="submit" class="p-2 bg-white/5 hover:bg-[#ff2d55]/10 text-gray-300 hover:text-[#ff2d55] rounded-xl transition-all duration-300" title="Logout">
                                <i data-lucide="log-out" class="w-5 h-5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                <span>Kembali</span>
            </a>
        </div>
        
        <!-- Event Header -->
        <div class="glass-card rounded-3xl overflow-hidden mb-8">
            @if($event->banner)
                <div class="relative h-64 md:h-96">
                    <img src="{{ Str::startsWith($event->banner, 'http') ? $event->banner : asset('storage/' . $event->banner) }}" 
                         alt="{{ $event->title }}" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0b0b0f] via-transparent to-transparent"></div>
                </div>
            @endif
            
            <div class="p-6 md:p-8">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#ff2d55]/20 text-[#ff2d55]">
                        {{ $event->category->name ?? 'Event' }}
                    </span>
                    <div class="flex items-center gap-2 text-sm text-gray-400">
                        <i data-lucide="star" class="w-4 h-4 text-yellow-400 fill-yellow-400"></i>
                        <span>4.8 (120 reviews)</span>
                    </div>
                </div>
                
                <h1 class="text-3xl md:text-4xl font-bold mb-4">{{ $event->title }}</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-300">
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar" class="w-5 h-5 text-[#ff2d55]"></i>
                        <span>{{ \Carbon\Carbon::parse($event->start_date)->format('l, d F Y') }}</span>
                        <span class="text-gray-500">|</span>
                        <span>{{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }} WIB</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-5 h-5 text-[#ff2d55]"></i>
                        <span>{{ $event->location }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-5 h-5 text-[#ff2d55]"></i>
                        <span>Doors open: 1 jam sebelum acara</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Event Description -->
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-card rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <i data-lucide="info" class="w-5 h-5 text-[#ff2d55]"></i>
                        Tentang Event
                    </h2>
                    <div class="prose prose-invert max-w-none">
                        <p class="text-gray-300 leading-relaxed">{{ $event->description }}</p>
                    </div>
                </div>
                
                <!-- Related Events -->
                @if(isset($relatedEvents) && $relatedEvents->count() > 0)
                <div class="glass-card rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <i data-lucide="calendar" class="w-5 h-5 text-[#ff2d55]"></i>
                        Event Terkait
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($relatedEvents as $related)
                        <a href="{{ url('/events/' . $related->id) }}" class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-colors">
                            @if($related->banner)
                                <img src="{{ asset('storage/' . $related->banner) }}" class="w-12 h-12 rounded-lg object-cover">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#ff2d55]/20 to-[#ff5e3a]/20 flex items-center justify-center">
                                    <i data-lucide="calendar" class="w-6 h-6 text-gray-500"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="font-semibold text-sm">{{ $related->title }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($related->start_date)->format('d M Y') }}</p>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-500"></i>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Ticket Selection -->
            <div class="lg:col-span-1">
                <div class="glass-card rounded-2xl p-6 sticky top-24">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <i data-lucide="ticket" class="w-5 h-5 text-[#ff2d55]"></i>
                        Pilih Tiket
                    </h2>
                    
                    <form action="{{ route('checkout.process') }}" method="POST" id="ticketForm">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                        
                        <div class="space-y-4 mb-6">
                            @forelse($event->tickets as $ticket)
                                <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="font-semibold">{{ $ticket->name }}</h3>
                                            @if($ticket->description)
                                                <p class="text-xs text-gray-500 mt-1">{{ $ticket->description }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-green-400">Rp {{ number_format($ticket->price, 0, ',', '.') }}</p>
                                            <p class="text-xs text-gray-500">Stok: {{ $ticket->stock }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($ticket->stock > 0)
                                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-white/10">
                                            <span class="text-sm text-gray-400">Jumlah</span>
                                            <div class="flex items-center gap-2">
                                                <button type="button" class="qty-minus w-8 h-8 rounded-lg bg-white/10 hover:bg-[#ff2d55]/20 text-white transition-colors" data-ticket-id="{{ $ticket->id }}">
                                                    <i data-lucide="minus" class="w-4 h-4 mx-auto"></i>
                                                </button>
                                                <input type="number" name="tickets[{{ $ticket->id }}]" 
                                                       class="qty-input w-16 text-center bg-transparent border border-white/10 rounded-lg py-1"
                                                       value="0" min="0" max="{{ $ticket->stock }}" step="1">
                                                <button type="button" class="qty-plus w-8 h-8 rounded-lg bg-white/10 hover:bg-[#ff2d55]/20 text-white transition-colors" data-ticket-id="{{ $ticket->id }}">
                                                    <i data-lucide="plus" class="w-4 h-4 mx-auto"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mt-3 pt-3 border-t border-white/10">
                                            <span class="text-sm text-red-400">Sold Out</span>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <i data-lucide="ticket-x" class="w-12 h-12 text-gray-500 mx-auto mb-3"></i>
                                    <p class="text-gray-400">Belum ada tiket tersedia</p>
                                </div>
                            @endforelse
                        </div>
                        
                        @if($event->tickets->where('stock', '>', 0)->count() > 0)
                            <div class="border-t border-white/10 pt-4 mb-4">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span id="totalPrice" class="text-green-400">Rp 0</span>
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] hover:opacity-90 text-white py-3 rounded-xl font-bold transition-all">
                                Beli Tiket
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
        
        // Update total price
        function updateTotal() {
            let total = 0;
            document.querySelectorAll('.qty-input').forEach(input => {
                const ticketId = input.getAttribute('name').match(/\d+/)[0];
                const priceElement = input.closest('.p-4').querySelector('.text-lg.font-bold');
                if (priceElement) {
                    const price = parseInt(priceElement.textContent.replace(/[^0-9]/g, ''));
                    const qty = parseInt(input.value) || 0;
                    total += price * qty;
                }
            });
            document.getElementById('totalPrice').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }
        
        // Quantity buttons
        document.querySelectorAll('.qty-plus').forEach(btn => {
            btn.addEventListener('click', function() {
                const ticketId = this.dataset.ticketId;
                const input = document.querySelector(`input[name="tickets[${ticketId}]"]`);
                const max = parseInt(input.getAttribute('max'));
                let value = parseInt(input.value) || 0;
                if (value < max) {
                    input.value = value + 1;
                    updateTotal();
                }
            });
        });
        
        document.querySelectorAll('.qty-minus').forEach(btn => {
            btn.addEventListener('click', function() {
                const ticketId = this.dataset.ticketId;
                const input = document.querySelector(`input[name="tickets[${ticketId}]"]`);
                let value = parseInt(input.value) || 0;
                if (value > 0) {
                    input.value = value - 1;
                    updateTotal();
                }
            });
        });
        
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', function() {
                const max = parseInt(this.getAttribute('max'));
                let value = parseInt(this.value) || 0;
                if (value > max) this.value = max;
                if (value < 0) this.value = 0;
                updateTotal();
            });
        });
        
        updateTotal();
    </script>
</body>
</html>