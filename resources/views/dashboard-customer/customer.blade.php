<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Dashboard - Tixflix</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <script src="https://unpkg.com/lucide@latest"></script>
    <script type="module" src="https://unpkg.com/@splinetool/viewer@1.9.7/build/spline-viewer.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b0b10; color: #ffffff; }
        
        spline-viewer::part(logo) { 
            display: none !important; 
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animate-float { animation: float 4s ease-in-out infinite; }
        
        @keyframes pulse-glow {
            0% { box-shadow: 0 0 0 0 rgba(255, 45, 85, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(255, 45, 85, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 45, 85, 0); }
        }
        .btn-glow:hover { animation: pulse-glow 1.5s infinite; }
        
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
        
        /* Modal styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
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
            padding: 24px;
            max-width: 400px;
            width: 90%;
            position: relative;
        }
        
        .modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .modal-close:hover {
            background: rgba(255, 45, 85, 0.3);
            transform: rotate(90deg);
        }
    </style>
</head>
<body class="bg-[#0b0b0f] text-gray-100 font-sans antialiased min-h-screen selection:bg-[#ff2d55] selection:text-white pb-20">

    <!-- Modal QR Code -->
    <div id="qrModal" class="modal-overlay" onclick="closeQRModal(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <button class="modal-close" onclick="closeQRModal()">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            
            <div class="text-center">
                <h3 class="text-xl font-bold mb-2">E-Ticket QR Code</h3>
                <p class="text-gray-400 text-sm mb-4" id="qrTicketCode">#TIX-XXXXXX</p>
                
                <div id="qrCodeContainer" class="bg-white p-4 rounded-2xl inline-block mb-4">
                    <div id="qrCode" class="w-48 h-48 flex items-center justify-center">
                        <!-- QR Code akan di-generate di sini -->
                        <div class="animate-pulse text-gray-400">Loading QR...</div>
                    </div>
                </div>
                
                <p class="text-xs text-gray-500">Tunjukkan QR code ini kepada petugas untuk scan</p>
                
                <button onclick="downloadQR()" class="mt-4 w-full glass-panel text-white py-3 rounded-xl font-bold text-sm hover:bg-white/10 transition-all duration-300">
                    <i data-lucide="download" class="w-4 h-4 inline-block mr-2"></i>
                    Download QR
                </button>
            </div>
        </div>
    </div>

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-[#ff2d55]/10 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] rounded-full bg-[#6a5af9]/10 blur-[120px]"></div>
    </div>

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

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-12">
        
        <section class="relative rounded-3xl overflow-hidden glass-card">
            <div class="absolute inset-0 bg-gradient-to-r from-[#0b0b0f] via-[#0b0b0f]/60 to-transparent z-10 pointer-events-none"></div>
            <div class="absolute inset-0 z-0 opacity-20 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-indigo-900 via-[#0b0b0f] to-[#0b0b0f] pointer-events-none"></div>
            
            <div class="relative z-20 flex flex-col md:flex-row items-center justify-between min-h-[350px] lg:min-h-[480px] p-8 md:p-10">
                <div class="w-full md:w-1/2 space-y-5 z-20">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full glass-panel border border-[#6a5af9]/40 text-xs font-semibold text-gray-200 mb-1">
                        <i data-lucide="star" class="w-3.5 h-3.5 text-yellow-400 fill-yellow-400"></i>
                        Platform Ticketing Event #1
                    </div>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight">
                        Temukan Event <br/>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#8e7aff] to-[#5946ea] drop-shadow-md">Terbaik Untukmu</span>
                    </h1>
                    <p class="text-gray-300 text-base leading-relaxed max-w-md font-medium">
                        Jelajahi ribuan konser, festival, dan pameran menarik. Amankan tiketmu sekarang dan ciptakan momen tak terlupakan!
                    </p>
                    <div class="pt-4 flex flex-wrap gap-3">
                        <button class="bg-[#5946ea] hover:bg-[#6a5af9] text-white px-6 py-3 rounded-xl font-bold text-sm shadow-[0_0_20px_rgba(89,70,234,0.4)] transform hover:-translate-y-1 transition-all duration-300">
                            Jelajahi Event
                        </button>
                        <button class="glass-panel text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-white/10 transition-all duration-300 border border-white/10 hover:border-white/20">
                            Lihat Trending
                        </button>
                    </div>
                </div>
                
                <div class="w-full md:w-1/2 flex items-center justify-center z-10 relative mt-8 md:mt-0">
                    <div id="robot" class="h-[300px] lg:h-[400px] w-full max-w-[450px] transition-transform duration-200" style="transform-style:preserve-3d">
                        <spline-viewer 
                            url="https://prod.spline.design/JRFiRBWDOYfhy2H0/scene.splinecode"
                            style="width:100%;height:100%">
                        </spline-viewer>
                    </div>
                </div>
            </div>
        </section>

        {{-- Explore Categories Section --}}
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold flex items-center gap-2">
                    <i data-lucide="compass" class="w-6 h-6 text-[#ff2d55]"></i>
                    Explore Categories
                </h2>
                <a href="#" class="text-sm text-[#ff2d55] hover:text-white transition-colors font-medium">All Categories</a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($categories as $category)
                    @php
                        $categoryName = strtolower($category->name);
                        $categorySlug = strtolower($category->slug ?? $category->name);
                        $routeName = '';
                        
                        // Tentukan route berdasarkan nama kategori
                        if (Str::contains($categoryName, 'konser') || Str::contains($categoryName, 'music') || Str::contains($categoryName, 'musik')) {
                            $routeName = route('concerts');
                        } elseif (Str::contains($categoryName, 'festival')) {
                            $routeName = route('festivals');
                        } elseif (Str::contains($categoryName, 'teknologi')) {
                            $routeName = url('/category/' . $category->id . '?type=technology');
                        } elseif (Str::contains($categoryName, 'kuliner')) {
                            $routeName = url('/category/' . $category->id . '?type=culinary');
                        } elseif (Str::contains($categoryName, 'workshop')) {
                            $routeName = url('/category/' . $category->id . '?type=workshop');
                        } else {
                            $routeName = url('/category/' . $category->id);
                        }
                    @endphp
                    
                    <a href="{{ $routeName }}" 
                       class="glass-card hover:bg-white/5 p-6 rounded-2xl cursor-pointer group transition-all duration-300 hover:-translate-y-1 text-center flex flex-col items-center gap-3">
                        <div class="w-14 h-14 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300">
                            @if(Str::contains(strtolower($category->name), 'konser') || Str::contains(strtolower($category->name), 'music') || Str::contains(strtolower($category->name), 'musik'))
                                <i data-lucide="mic-2" class="w-7 h-7"></i>
                            @elseif(Str::contains(strtolower($category->name), 'festival'))
                                <i data-lucide="tent" class="w-7 h-7"></i>
                            @elseif(Str::contains(strtolower($category->name), 'olahraga') || Str::contains(strtolower($category->name), 'sport'))
                                <i data-lucide="dribbble" class="w-7 h-7"></i>
                            @elseif(Str::contains(strtolower($category->name), 'pameran') || Str::contains(strtolower($category->name), 'exhibition'))
                                <i data-lucide="palette" class="w-7 h-7"></i>
                            @elseif(Str::contains(strtolower($category->name), 'teknologi') || Str::contains(strtolower($category->name), 'technology'))
                                <i data-lucide="cpu" class="w-7 h-7"></i>
                            @elseif(Str::contains(strtolower($category->name), 'kuliner') || Str::contains(strtolower($category->name), 'culinary'))
                                <i data-lucide="utensils" class="w-7 h-7"></i>
                            @elseif(Str::contains(strtolower($category->name), 'workshop'))
                                <i data-lucide="graduation-cap" class="w-7 h-7"></i>
                            @else
                                <i data-lucide="ticket" class="w-7 h-7"></i>
                            @endif
                        </div>
                        <span class="font-medium">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Trending Now Section --}}
            <section class="lg:col-span-2">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold flex items-center gap-2">
                        <i data-lucide="flame" class="w-6 h-6 text-[#ff5e3a]"></i>
                        Trending Now
                    </h2>
                    <a href="#" class="text-sm text-[#ff2d55] hover:text-white transition-colors font-medium">View All</a>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @forelse($pilihanEvents as $event)
                        <a href="{{ url('/events/' . $event->id) }}" 
                           class="glass-card rounded-2xl overflow-hidden group cursor-pointer relative block transform hover:-translate-y-1 transition-all duration-300">

                            <!-- Rating -->
                            <div class="absolute top-3 right-3 z-20 glass-panel px-3 py-1.5 rounded-full text-xs font-bold text-white flex items-center gap-1 pointer-events-none">
                                <i data-lucide="star" class="w-3 h-3 text-yellow-400 fill-yellow-400"></i> 4.8
                            </div>

                            <!-- Banner -->
                            <div class="aspect-video relative overflow-hidden bg-[#1e1e28]">
                                @if($event->banner)
                                    <img 
                                        src="{{ Str::startsWith($event->banner, 'http') ? $event->banner : asset('storage/' . $event->banner) }}" 
                                        alt="{{ $event->title }}" 
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                    >
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-[#6a5af9]/40 to-[#0b0b0f] flex items-center justify-center group-hover:scale-110 transition-transform duration-700">
                                        <i data-lucide="mic" class="w-12 h-12 text-white/20"></i>
                                    </div>
                                @endif

                                <!-- Gradient overlay -->
                                <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-[#0b0b0f] to-transparent z-10 pointer-events-none"></div>
                            </div>

                            <!-- Content -->
                            <div class="p-5 relative z-20 -mt-6">
                                <span class="text-xs font-bold tracking-wider text-[#6a5af9] uppercase mb-1 block">
                                    {{ $event->category->name ?? 'Event' }}
                                </span>

                                <h3 class="text-xl font-bold mb-2 group-hover:text-[#6a5af9] transition-colors truncate">
                                    {{ $event->title }}
                                </h3>

                                <div class="flex items-center gap-4 text-sm text-gray-400 mb-4">
                                    <span class="flex items-center gap-1.5">
                                        <i data-lucide="calendar" class="w-4 h-4"></i> 
                                        {{ \Carbon\Carbon::parse($event->start_date)->format('M d') }}
                                    </span>

                                    <span class="flex items-center gap-1.5 truncate" title="{{ $event->location }}">
                                        <i data-lucide="map-pin" class="w-4 h-4"></i> 
                                        {{ Str::limit($event->location, 15) }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-white">
                                        @if($event->tickets && $event->tickets->count() > 0)
                                            Rp {{ number_format($event->tickets->min('price'), 0, ',', '.') }}
                                        @else
                                            Sold Out
                                        @endif
                                    </span>

                                    <!-- Arrow -->
                                    <div class="bg-white/10 group-hover:bg-[#6a5af9] text-white p-2.5 rounded-xl transition-colors duration-300">
                                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-2 text-center py-10 text-gray-500 glass-card rounded-2xl">
                            Belum ada event yang tersedia saat ini.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Next Up Section --}}
            <section class="lg:col-span-1">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold flex items-center gap-2">
                        <i data-lucide="ticket" class="w-6 h-6 text-white"></i> Next Up
                    </h2>
                    <a href="{{ route('my-tickets') }}" class="text-sm text-[#ff2d55] hover:text-white transition-colors font-medium">View All</a>
                </div>
                
                {{-- Ambil tiket user yang akan datang (next upcoming) --}}
                @php
                    $upcomingTickets = App\Models\Eticket::with(['ticket.event'])
                        ->where('user_id', auth()->id())
                        ->whereHas('ticket.event', function($query) {
                            $query->where('start_date', '>=', now());
                        })
                        ->orderBy('created_at', 'desc')
                        ->take(1)
                        ->get();
                @endphp
                
                @forelse($upcomingTickets as $eticket)
                    @php
                        $event = $eticket->ticket->event;
                        $isTomorrow = \Carbon\Carbon::parse($event->start_date)->isTomorrow();
                        $dateLabel = $isTomorrow ? 'TOMORROW' : \Carbon\Carbon::parse($event->start_date)->format('D, M d');
                    @endphp
                    
                    <div class="glass-card rounded-3xl p-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-[#ff2d55]/10 rounded-full blur-[40px] -mr-10 -mt-10"></div>
                        
                        <div class="relative z-10 border-b border-white/10 pb-5 mb-5">
                            <div class="flex gap-4">
                                <div class="w-16 h-20 rounded-xl overflow-hidden shrink-0 bg-gray-800">
                                    @if($event->banner)
                                        <img src="{{ Str::startsWith($event->banner, 'http') ? $event->banner : asset('storage/' . $event->banner) }}" 
                                             alt="{{ $event->title }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-orange-500/40 to-[#0b0b0f] flex items-center justify-center">
                                            <i data-lucide="ticket" class="w-6 h-6 text-white/30"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex-1">
                                    <span class="text-[10px] sm:text-xs font-bold text-[#ff2d55] bg-[#ff2d55]/10 px-2 py-1 rounded-md mb-1.5 inline-block">
                                        {{ $dateLabel }}
                                    </span>
                                    <h4 class="font-bold text-white leading-tight mb-1 text-sm sm:text-base">{{ $event->title }}</h4>
                                    <p class="text-[10px] sm:text-xs text-gray-400 flex items-center gap-1.5 mb-2">
                                        <i data-lucide="clock" class="w-3 h-3"></i> 
                                        {{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }} WIB
                                    </p>
                                    <p class="text-[10px] sm:text-xs text-gray-400 flex items-center gap-1.5">
                                        <i data-lucide="map-pin" class="w-3 h-3"></i> 
                                        {{ $event->location }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-dashed border-white/20 flex justify-between items-center">
                                <div class="text-xs sm:text-sm font-bold font-mono tracking-widest text-[#ff2d55]">
                                    {{ $eticket->ticket_code }}
                                </div>
                                <button onclick="showQR('{{ $eticket->ticket_code }}', '{{ $event->title }}')" 
                                        class="text-[10px] sm:text-xs font-bold text-white bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-full transition-colors">
                                    Show QR
                                </button>
                            </div>
                        </div>
                        
                        {{-- Decorative circles --}}
                        <div class="absolute left-[-10px] top-[140px] w-5 h-5 bg-[#0b0b0f] rounded-full shadow-[inset_0_0_8px_rgba(0,0,0,0.5)]"></div>
                        <div class="absolute right-[-10px] top-[140px] w-5 h-5 bg-[#0b0b0f] rounded-full shadow-[inset_0_0_8px_rgba(0,0,0,0.5)]"></div>
                    </div>
                @empty
                    <div class="glass-card rounded-3xl p-8 text-center">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-white/5 flex items-center justify-center">
                            <i data-lucide="ticket" class="w-8 h-8 text-gray-500"></i>
                        </div>
                        <p class="text-gray-400 text-sm mb-2">Belum ada tiket yang akan datang</p>
                        <a href="{{ route('dashboard') }}" class="text-[#ff2d55] text-sm hover:text-white transition-colors">
                            Cari event sekarang →
                        </a>
                    </div>
                @endforelse

                {{-- Hitung total tiket yang akan datang --}}
                @php
                    $totalUpcoming = App\Models\Eticket::where('user_id', auth()->id())
                        ->whereHas('ticket.event', function($query) {
                            $query->where('start_date', '>=', now());
                        })
                        ->count();
                @endphp
                
                @if($totalUpcoming > 1)
                    <div class="mt-4 p-4 rounded-xl border border-dashed border-white/20 text-center text-sm text-gray-400 bg-white/5 hover:bg-white/10 transition-colors cursor-pointer"
                         onclick="window.location.href='{{ route('my-tickets') }}'">
                        + You have {{ $totalUpcoming - 1 }} more upcoming {{ $totalUpcoming - 1 > 1 ? 'tickets' : 'ticket' }}
                    </div>
                @endif
            </section>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <script>
        lucide.createIcons();

        // Modal functions
        let currentQRCode = null;
        
        function showQR(ticketCode, eventTitle) {
            const modal = document.getElementById('qrModal');
            const qrContainer = document.getElementById('qrCode');
            const ticketCodeEl = document.getElementById('qrTicketCode');
            
            ticketCodeEl.textContent = ticketCode;
            
            // Generate QR Code
            qrContainer.innerHTML = '';
            QRCode.toCanvas(qrContainer, ticketCode, {
                width: 200,
                margin: 2,
                color: {
                    dark: '#000000',
                    light: '#ffffff'
                }
            }, function(error) {
                if (error) {
                    console.error(error);
                    qrContainer.innerHTML = '<div class="text-red-500">Error generating QR</div>';
                }
            });
            
            modal.style.display = 'flex';
            lucide.createIcons();
        }
        
        function closeQRModal(event) {
            if (event && event.target === document.getElementById('qrModal')) {
                document.getElementById('qrModal').style.display = 'none';
            } else if (!event) {
                document.getElementById('qrModal').style.display = 'none';
            }
        }
        
        function downloadQR() {
            const canvas = document.querySelector('#qrCode canvas');
            if (!canvas) return;
            
            const link = document.createElement('a');
            link.download = 'ticket-qr.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        }

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeQRModal();
            }
        });

        // Hide Spline logo
        window.addEventListener('load', function() {
            setTimeout(() => {
                const splineViewer = document.querySelector('spline-viewer');
                if (splineViewer && splineViewer.shadowRoot) {
                    const logo = splineViewer.shadowRoot.querySelector('#logo');
                    if (logo) {
                        logo.style.display = 'none';
                        logo.remove();
                    }
                }
            }, 1000);
        });
    </script>
</body>
</html>