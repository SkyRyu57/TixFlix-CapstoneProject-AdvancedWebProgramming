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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b0b10; }
        .glass-panel {
            background: rgba(18, 18, 24, 0.6);
            backdrop-filter: blur(16px);
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
        
        /* Animation for QR hover */
        .qr-hover {
            transition: all 0.3s ease;
        }
        .qr-hover:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(255, 45, 85, 0.3);
        }
        
        /* Print button styling */
        .btn-print {
            transition: all 0.3s ease;
        }
        .btn-print:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-[#0b0b0f] text-gray-100 font-sans antialiased min-h-screen">

    <!-- Background Effects -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-[#ff2d55]/10 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] rounded-full bg-[#6a5af9]/10 blur-[120px]"></div>
    </div>

    <!-- Navbar -->
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
                    <a href="{{ route('my-tickets') }}" class="text-white font-medium relative group">
                        My Tickets
                        <span class="absolute -bottom-1.5 left-0 w-full h-0.5 bg-[#ff2d55] rounded-full"></span>
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
            <div class="mb-8 bg-green-500/10 border-l-4 border-green-500 text-green-500 p-4 rounded-r-lg flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <p class="font-bold">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-8 bg-red-500/10 border-l-4 border-red-500 text-red-500 p-4 rounded-r-lg flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <p class="font-bold">{{ session('error') }}</p>
            </div>
        @endif

        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-4xl font-extrabold mb-2">My E-Tickets</h1>
                <p class="text-gray-400">Tunjukkan QR code ini saat berada di lokasi acara.</p>
            </div>
            <div class="flex gap-3">
                <button onclick="window.location.href='{{ route('dashboard') }}'" 
                        class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl font-medium transition-colors flex items-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Cari Event Lain
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse($myTickets as $eticket)
                @php
                    $event = $eticket->ticket->event;
                    $isUpcoming = \Carbon\Carbon::parse($event->start_date)->isFuture();
                    $statusColor = $eticket->is_scanned ? 'bg-gray-500/20 text-gray-400' : ($isUpcoming ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400');
                    $statusText = $eticket->is_scanned ? 'USED' : ($isUpcoming ? 'UPCOMING' : 'EXPIRED');
                @endphp
                <div class="glass-card rounded-3xl p-6 md:p-8 relative overflow-hidden flex flex-col md:flex-row gap-6 items-center hover:border-[#ff2d55]/30 transition-all duration-300 group">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-[#ff2d55]/10 rounded-full blur-[40px] pointer-events-none"></div>
                    
                    <!-- QR Code with hover effect -->
                    <div class="w-32 h-32 bg-white p-2 rounded-xl shrink-0 flex items-center justify-center shadow-lg shadow-black/50 qr-hover cursor-pointer"
                         onclick="window.open('https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ $eticket->ticket_code }}', '_blank')">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $eticket->ticket_code }}" 
                             alt="QR Code" 
                             class="w-full h-full object-contain">
                    </div>

                    <div class="flex-1 w-full border-t border-dashed border-white/20 pt-6 md:border-t-0 md:border-l md:pt-0 md:pl-6 relative z-10">
                        <div class="flex justify-between items-start mb-2 flex-wrap gap-2">
                            <span class="text-xs font-bold {{ $statusColor }} px-2.5 py-1 rounded-md">
                                {{ $statusText }}
                            </span>
                            <span class="text-xs font-mono text-gray-400 bg-black/30 px-2 py-1 rounded-md">
                                {{ $eticket->ticket_code }}
                            </span>
                        </div>
                        <h3 class="text-xl font-bold mb-1 text-white group-hover:text-[#ff2d55] transition-colors">
                            {{ $event->title ?? 'Unknown Event' }}
                        </h3>
                        <p class="text-sm text-gray-400 mb-4">{{ $eticket->ticket->name ?? 'Regular Ticket' }}</p>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm bg-[#0b0b0f]/50 p-3 rounded-xl border border-white/5">
                            <div>
                                <span class="text-gray-500 text-xs block mb-0.5">Date & Time</span>
                                <span class="font-semibold text-white">
                                    <i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>
                                    {{ \Carbon\Carbon::parse($event->start_date ?? now())->format('d M Y') }}
                                    <br>
                                    <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                                    {{ \Carbon\Carbon::parse($event->start_date ?? now())->format('H:i') }} WIB
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs block mb-0.5">Location</span>
                                <span class="font-semibold text-white truncate block" title="{{ $event->location ?? '-' }}">
                                    <i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i>
                                    {{ $event->location ?? '-' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Ticket Price -->
                        <div class="mt-3 flex justify-between items-center">
                            <span class="text-xs text-gray-500">Ticket Price</span>
                            <span class="text-sm font-bold text-green-400">Rp {{ number_format($eticket->ticket->price ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="absolute bottom-4 right-4 flex gap-2">
                        <a href="{{ route('ticket.print', $eticket->ticket_code) }}" 
                           class="btn-print p-2 bg-white/10 hover:bg-green-500/20 rounded-lg transition-all duration-300" 
                           title="Print E-Ticket">
                            <i data-lucide="printer" class="w-4 h-4"></i>
                        </a>
                        <button onclick="copyTicketCode('{{ $eticket->ticket_code }}')" 
                                class="btn-print p-2 bg-white/10 hover:bg-blue-500/20 rounded-lg transition-all duration-300" 
                                title="Copy Ticket Code">
                            <i data-lucide="copy" class="w-4 h-4"></i>
                        </button>
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
                    <a href="{{ route('dashboard') }}" class="bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] hover:opacity-90 text-white px-6 py-3 rounded-xl font-bold transition-all inline-flex items-center gap-2">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        Cari Event Sekarang
                    </a>
                </div>
            @endforelse
        </div>
        
        <!-- Ticket Statistics -->
        @if($myTickets->count() > 0)
        <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="glass-card rounded-2xl p-4 text-center">
                <i data-lucide="ticket" class="w-6 h-6 text-[#ff2d55] mx-auto mb-2"></i>
                <p class="text-2xl font-bold">{{ $myTickets->count() }}</p>
                <p class="text-xs text-gray-400">Total Tickets</p>
            </div>
            <div class="glass-card rounded-2xl p-4 text-center">
                <i data-lucide="calendar" class="w-6 h-6 text-green-400 mx-auto mb-2"></i>
                <p class="text-2xl font-bold">
                    {{ $myTickets->filter(function($t) { return \Carbon\Carbon::parse($t->ticket->event->start_date)->isFuture() && !$t->is_scanned; })->count() }}
                </p>
                <p class="text-xs text-gray-400">Upcoming Events</p>
            </div>
            <div class="glass-card rounded-2xl p-4 text-center">
                <i data-lucide="check-circle" class="w-6 h-6 text-yellow-400 mx-auto mb-2"></i>
                <p class="text-2xl font-bold">
                    {{ $myTickets->filter(function($t) { return $t->is_scanned; })->count() }}
                </p>
                <p class="text-xs text-gray-400">Already Used</p>
            </div>
        </div>
        @endif
    </div>

    <script>
        lucide.createIcons();
        
        function copyTicketCode(code) {
            navigator.clipboard.writeText(code).then(function() {
                // Show temporary notification
                const notification = document.createElement('div');
                notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-pulse';
                notification.innerHTML = '✓ Ticket code copied!';
                document.body.appendChild(notification);
                setTimeout(() => notification.remove(), 2000);
            });
        }
    </script>
</body>
</html>