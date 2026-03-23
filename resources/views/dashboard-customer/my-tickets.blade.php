<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Tickets - Tixflix</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: linear-gradient(145deg, rgba(30, 30, 40, 0.8) 0%, rgba(15, 15, 20, 0.9) 100%); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3); backdrop-filter: blur(8px); }
        .text-gradient {
            background: linear-gradient(135deg, #ff2d55 0%, #ff5e3a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="bg-[#0b0b0f] text-gray-100 font-sans antialiased min-h-screen">

    <nav class="sticky top-0 z-50 glass-panel border-b border-white/10 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3 cursor-pointer group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center shadow-lg shadow-[#ff2d55]/30 group-hover:scale-105 transition-transform duration-300">
                        <i data-lucide="ticket" class="w-6 h-6 text-white transform -rotate-12"></i>
                    </div>
                    <span class="text-xl md:text-2xl font-bold tracking-tight">Tix<span class="text-gradient">flix</span></span>
                </div>

                <div class="hidden md:flex space-x-8 items-center">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-white font-medium relative group' : 'text-gray-400 hover:text-white font-medium transition-colors' }}">
                        Home
                        @if(request()->routeIs('dashboard')) <span class="absolute -bottom-1.5 left-0 w-full h-0.5 bg-[#ff2d55] rounded-full"></span> @endif
                    </a>
                    <a href="{{ route('concerts') }}" class="{{ request()->routeIs('concerts') ? 'text-white font-medium relative group' : 'text-gray-400 hover:text-white font-medium transition-colors' }}">
                        Concerts
                        @if(request()->routeIs('concerts')) <span class="absolute -bottom-1.5 left-0 w-full h-0.5 bg-[#ff2d55] rounded-full"></span> @endif
                    </a>
                    <a href="{{ route('festivals') }}" class="{{ request()->routeIs('festivals') ? 'text-white font-medium relative group' : 'text-gray-400 hover:text-white font-medium transition-colors' }}">
                        Festivals
                        @if(request()->routeIs('festivals')) <span class="absolute -bottom-1.5 left-0 w-full h-0.5 bg-[#ff2d55] rounded-full"></span> @endif
                    </a>
                    <a href="{{ route('my-tickets') }}" class="{{ request()->routeIs('my-tickets') ? 'text-white font-medium relative group' : 'text-gray-400 hover:text-white font-medium transition-colors' }}">
                        My Tickets
                        @if(request()->routeIs('my-tickets')) <span class="absolute -bottom-1.5 left-0 w-full h-0.5 bg-[#ff2d55] rounded-full"></span> @endif
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

    <div class="pt-28 pb-20 px-6 max-w-7xl mx-auto">
        
        @if(session('success'))
            <div class="mb-8 bg-[#ff2d55]/10 border border-[#ff2d55]/30 text-[#ff2d55] px-6 py-4 rounded-xl flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <p class="font-bold">{{ session('success') }}</p>
            </div>
        @endif

        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-4xl font-extrabold mb-2">My E-Tickets</h1>
                <p class="text-gray-400">Tunjukkan QR code ini saat berada di lokasi acara.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse($myTickets as $eticket)
                <div class="glass-card rounded-3xl p-6 md:p-8 relative overflow-hidden flex flex-col md:flex-row gap-6 items-center hover:border-[#ff2d55]/30 transition-colors duration-300">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-[#ff2d55]/10 rounded-full blur-[40px] pointer-events-none"></div>
                    
                    <div class="w-32 h-32 bg-white p-2 rounded-xl shrink-0 flex items-center justify-center shadow-lg shadow-black/50">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $eticket->ticket_code }}" alt="QR Code" class="w-full h-full object-contain">
                    </div>

                    <div class="flex-1 w-full border-t border-dashed border-white/20 pt-6 md:border-t-0 md:border-l md:pt-0 md:pl-6 relative z-10">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold {{ $eticket->is_scanned ? 'text-gray-400 bg-gray-800' : 'text-[#ff2d55] bg-[#ff2d55]/10 border-[#ff2d55]/20 border' }} px-2.5 py-1 rounded-md">
                                {{ $eticket->is_scanned ? 'USED' : 'UPCOMING' }}
                            </span>
                            <span class="text-xs font-mono text-gray-400 bg-black/30 px-2 py-1 rounded-md">ID: {{ $eticket->ticket_code }}</span>
                        </div>
                        <h3 class="text-xl font-bold mb-1 text-white">{{ $eticket->ticket->event->title ?? 'Unknown Event' }}</h3>
                        <p class="text-sm text-gray-400 mb-4">{{ $eticket->ticket->name ?? 'Regular Ticket' }}</p>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm bg-[#0b0b0f]/50 p-3 rounded-xl border border-white/5">
                            <div>
                                <span class="text-gray-500 text-xs block mb-0.5">Date & Time</span>
                                <span class="font-semibold text-white">{{ \Carbon\Carbon::parse($eticket->ticket->event->start_date ?? now())->format('d M, H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs block mb-0.5">Location</span>
                                <span class="font-semibold text-white truncate block" title="{{ $eticket->ticket->event->location ?? '-' }}">{{ $eticket->ticket->event->location ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="absolute left-[-15px] top-1/2 -translate-y-1/2 w-8 h-8 bg-[#0b0b0f] rounded-full hidden md:block border-r border-white/10 shadow-[inset_0_0_10px_rgba(0,0,0,0.5)]"></div>
                    <div class="absolute right-[-15px] top-1/2 -translate-y-1/2 w-8 h-8 bg-[#0b0b0f] rounded-full hidden md:block border-l border-white/10 shadow-[inset_0_0_10px_rgba(0,0,0,0.5)]"></div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 glass-card rounded-3xl">
                    <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-4 border border-white/10">
                        <i data-lucide="ticket" class="w-10 h-10 text-gray-500"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Belum ada tiket</h3>
                    <p class="text-gray-400 mb-6">Kamu belum memiliki tiket apapun. Yuk, cari event seru!</p>
                    <a href="{{ route('dashboard') }}" class="bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-xl font-bold transition">Cari Event Sekarang</a>
                </div>
            @endforelse
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>