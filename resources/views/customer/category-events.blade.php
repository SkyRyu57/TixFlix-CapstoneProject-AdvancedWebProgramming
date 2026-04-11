<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $category->name }} - Tixflix</title>

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
<body class="bg-[#0b0b0f] text-gray-100 font-sans antialiased min-h-screen selection:bg-[#ff2d55] selection:text-white">

    <!-- Background Effects -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-[#ff2d55]/10 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] rounded-full bg-[#6a5af9]/10 blur-[120px]"></div>
    </div>

    <!-- Navigation -->
    <nav class="sticky top-0 z-50 glass-panel border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3 cursor-pointer group" onclick="window.location.href='{{ route('dashboard') }}'">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center shadow-lg shadow-[#ff2d55]/30 group-hover:scale-105 transition-transform">
                        <i data-lucide="ticket" class="w-6 h-6 text-white transform -rotate-12"></i>
                    </div>
                    <span class="text-xl md:text-2xl font-bold tracking-tight">Tix<span class="text-gradient">flix</span></span>
                </div>

                <div class="hidden md:flex space-x-8 items-center">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition-colors">Home</a>
                    <a href="{{ route('concerts') }}" class="text-gray-400 hover:text-white transition-colors">Concerts</a>
                    <a href="{{ route('festivals') }}" class="text-gray-400 hover:text-white transition-colors">Festivals</a>
                    <a href="{{ route('my-tickets') }}" class="text-gray-400 hover:text-white transition-colors">My Tickets</a>
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

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header Kategori -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-gray-400 text-sm mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-white transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span class="text-white">{{ $category->name }}</span>
            </div>
            
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold flex items-center gap-3">
                    @php
                        $icon = 'ticket';
                        if(Str::contains(strtolower($category->name), 'konser') || Str::contains(strtolower($category->name), 'music')) {
                            $icon = 'mic-2';
                        } elseif(Str::contains(strtolower($category->name), 'festival')) {
                            $icon = 'tent';
                        } elseif(Str::contains(strtolower($category->name), 'olahraga')) {
                            $icon = 'dribbble';
                        } elseif(Str::contains(strtolower($category->name), 'teknologi')) {
                            $icon = 'cpu';
                        } elseif(Str::contains(strtolower($category->name), 'kuliner')) {
                            $icon = 'utensils';
                        } elseif(Str::contains(strtolower($category->name), 'workshop')) {
                            $icon = 'graduation-cap';
                        }
                    @endphp
                    <i data-lucide="{{ $icon }}" class="w-8 h-8 text-[#ff2d55]"></i>
                    {{ $category->name }}
                </h1>
                <span class="text-gray-400">{{ $events->total() }} events ditemukan</span>
            </div>
        </div>

        <!-- Grid Events -->
        @if($events->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($events as $event)
                    <a href="{{ url('/events/' . $event->id) }}" 
                       class="glass-card rounded-2xl overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                        
                        @if($event->banner)
                            <div class="aspect-video overflow-hidden">
                                <img src="{{ Str::startsWith($event->banner, 'http') ? $event->banner : asset('storage/' . $event->banner) }}" 
                                     alt="{{ $event->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            </div>
                        @else
                            <div class="aspect-video bg-gradient-to-br from-[#6a5af9]/40 to-[#0b0b0f] flex items-center justify-center">
                                <i data-lucide="image" class="w-12 h-12 text-white/20"></i>
                            </div>
                        @endif
                        
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2 group-hover:text-[#6a5af9] transition-colors line-clamp-2">
                                {{ $event->title }}
                            </h3>
                            
                            <div class="space-y-2 text-sm text-gray-400 mb-4">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                    {{ \Carbon\Carbon::parse($event->start_date)->format('d M Y, H:i') }} WIB
                                </div>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                                    {{ $event->location }}
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-white">
                                    @if($event->tickets && $event->tickets->count() > 0)
                                        Rp {{ number_format($event->tickets->min('price'), 0, ',', '.') }}
                                    @else
                                        <span class="text-sm text-gray-500">Sold Out</span>
                                    @endif
                                </span>
                                
                                <div class="bg-white/10 group-hover:bg-[#6a5af9] p-2 rounded-xl transition-colors">
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $events->links() }}
            </div>
        @else
            <div class="glass-card rounded-3xl p-16 text-center">
                <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-white/5 flex items-center justify-center">
                    <i data-lucide="calendar-x" class="w-12 h-12 text-gray-500"></i>
                </div>
                <h3 class="text-2xl font-bold mb-2">Belum Ada Event</h3>
                <p class="text-gray-400 mb-6">Belum ada event di kategori {{ $category->name }} saat ini</p>
                <a href="{{ route('dashboard') }}" 
                   class="inline-block bg-[#5946ea] hover:bg-[#6a5af9] text-white px-6 py-3 rounded-xl font-bold transition-all">
                    Kembali ke Dashboard
                </a>
            </div>
        @endif
    </main>

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