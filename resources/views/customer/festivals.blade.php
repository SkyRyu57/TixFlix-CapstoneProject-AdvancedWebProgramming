<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Festivals - Tixflix</title>
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
        
        /* Dropdown styles */
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 8px;
            min-width: 200px;
            z-index: 100;
        }
    </style>
</head>
<body class="bg-[#0b0b0f] text-gray-100 font-sans antialiased min-h-screen">

    <!-- Background Effects -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-[#ff2d55]/10 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] rounded-full bg-[#6a5af9]/10 blur-[120px]"></div>
    </div>

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
                    
                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <button id="userMenuBtn" class="focus:outline-none group">
                            <div class="relative flex items-center gap-3 md:pl-4 md:border-l border-white/10">
                                <div class="hidden md:block text-right">
                                    <div class="text-sm font-semibold group-hover:text-[#ff2d55] transition-colors">{{ auth()->user()->name ?? 'Guest User' }}</div>
                                    <div class="text-xs text-gray-400">Event Explorer</div>
                                </div>
                                <div class="w-10 h-10 rounded-full border-2 border-[#1e1e28] bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center text-white font-bold hover:border-[#ff2d55] transition-colors shadow-lg shadow-[#ff2d55]/20">
                                    {{ substr(auth()->user()->name ?? 'G', 0, 1) }}
                                </div>
                            </div>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="userDropdown" class="dropdown-menu glass-card rounded-xl overflow-hidden hidden">
                            <div class="p-3 border-b border-white/10">
                                <p class="font-semibold text-sm">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition-colors">
                                <i data-lucide="user" class="w-4 h-4"></i>
                                <span class="text-sm">My Profile</span>
                            </a>
                            <a href="{{ route('my-tickets') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition-colors">
                                <i data-lucide="ticket" class="w-4 h-4"></i>
                                <span class="text-sm">My Tickets</span>
                            </a>
                            <div class="border-t border-white/10"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition-colors w-full text-left">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span class="text-sm">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-28 pb-20 px-6 max-w-7xl mx-auto">
        <div class="mb-10 text-center md:text-left">
            <h1 class="text-4xl font-extrabold mb-4">Festival <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a]">Pilihan</span></h1>
            <p class="text-gray-400 text-lg">Nikmati euforia festival terbesar tahun ini bersama teman-temanmu!</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @forelse($festivals as $event)
                <div class="glass-card rounded-3xl overflow-hidden relative min-h-[350px] flex flex-col justify-end p-8 border border-[#ff5e3a]/20 group">
                    @if($event->banner)
                        <img src="{{ asset('storage/' . $event->banner) }}" alt="{{ $event->title }}" class="absolute inset-0 w-full h-full object-cover z-0 opacity-40 group-hover:scale-105 transition-transform duration-700">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0b0b0f] via-[#0b0b0f]/80 to-transparent z-0 pointer-events-none"></div>
                    
                    <div class="relative z-10 w-full">
                        <span class="bg-[#ff5e3a] text-white px-3 py-1 rounded-full text-xs font-bold mb-4 inline-block">{{ $event->category->name ?? 'Festival' }}</span>
                        <h2 class="text-3xl md:text-4xl font-bold mb-2">{{ $event->title }}</h2>
                        <div class="flex items-center gap-4 text-sm text-gray-300 mb-4">
                            <span><i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i> {{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}</span>
                            <span><i data-lucide="map-pin" class="w-4 h-4 inline mr-1"></i> {{ $event->location }}</span>
                        </div>
                        <p class="text-gray-400 mb-6 max-w-lg line-clamp-2">{{ $event->description }}</p>
                        <a href="{{ url('/events/' . $event->id) }}" class="inline-block bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] hover:opacity-90 text-white px-8 py-3.5 rounded-xl font-bold shadow-lg shadow-[#ff2d55]/30 transition-all">
                            Cek Lineup & Tiket
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-400 py-12 glass-card rounded-3xl">
                    <i data-lucide="tent" class="w-12 h-12 mx-auto mb-4 opacity-50"></i>
                    <p class="text-lg">Belum ada festival yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        lucide.createIcons();
        
        // Dropdown toggle
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userDropdown');
        
        if (userMenuBtn) {
            userMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });
        }
        
        document.addEventListener('click', function() {
            if (userDropdown) {
                userDropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>