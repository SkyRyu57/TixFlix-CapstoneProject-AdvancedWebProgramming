<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Detail - Organizer Dashboard</title>
    
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
        .sidebar-item { transition: all 0.3s ease; }
        .sidebar-item:hover { background: rgba(255, 45, 85, 0.1); transform: translateX(5px); }
        .sidebar-item.active { background: linear-gradient(90deg, rgba(255, 45, 85, 0.2) 0%, rgba(255, 94, 58, 0.1) 100%); border-left: 3px solid #ff2d55; }
        
        /* Dropdown untuk sidebar */
        .profile-dropdown {
            position: absolute;
            bottom: 100%;
            left: 0;
            margin-bottom: 8px;
            width: 100%;
            z-index: 100;
        }
    </style>
</head>
<body class="bg-[#0b0b0f] text-gray-100">
    
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-[#ff2d55]/10 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] rounded-full bg-[#6a5af9]/10 blur-[120px]"></div>
    </div>
    
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-72 glass-panel border-r border-white/10 flex flex-col fixed h-full overflow-y-auto">
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='{{ route('organizer.dashboard') }}'">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center">
                        <i data-lucide="ticket" class="w-6 h-6 text-white transform -rotate-12"></i>
                    </div>
                    <span class="text-xl font-bold">Tix<span class="text-gradient">flix</span></span>
                </div>
                <p class="text-xs text-gray-400 mt-2">Organizer Dashboard</p>
            </div>
            
            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('organizer.dashboard') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('organizer.events') }}" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    <span>Events</span>
                </a>
                
                <a href="{{ route('organizer.event.create') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    <span>Create Event</span>
                </a>
                
                <a href="{{ route('organizer.attendees') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span>Attendees</span>
                </a>
            </nav>
            
            <!-- Profile Section with Dropdown -->
            <div class="p-4 border-t border-white/10 relative">
                <div class="relative">
                    <button id="organizerMenuBtn" class="w-full flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center font-bold">
                            {{ substr(auth()->user()->name ?? 'O', 0, 1) }}
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                        </div>
                        <i data-lucide="chevron-up" class="w-4 h-4 text-gray-400"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="organizerDropdown" class="profile-dropdown glass-card rounded-xl overflow-hidden hidden">
                        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition-colors">
                            <i data-lucide="user" class="w-4 h-4"></i>
                            <span class="text-sm">My Profile</span>
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
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 ml-72 overflow-y-auto">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                    <a href="{{ route('organizer.events') }}" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold">Event Details</h1>
                        <p class="text-gray-400 mt-1">{{ $event->title }}</p>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="glass-card rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-full bg-green-500/20 flex items-center justify-center">
                                <i data-lucide="ticket" class="w-6 h-6 text-green-400"></i>
                            </div>
                            <span class="text-2xl font-bold">{{ number_format($ticketsSold) }}</span>
                        </div>
                        <h3 class="text-gray-400 text-sm">Tickets Sold</h3>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-full bg-yellow-500/20 flex items-center justify-center">
                                <i data-lucide="wallet" class="w-6 h-6 text-yellow-400"></i>
                            </div>
                            <span class="text-2xl font-bold">Rp {{ number_format($revenue, 0, ',', '.') }}</span>
                        </div>
                        <h3 class="text-gray-400 text-sm">Total Revenue</h3>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center">
                                <i data-lucide="calendar" class="w-6 h-6 text-blue-400"></i>
                            </div>
                            <span class="text-2xl font-bold">{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}</span>
                        </div>
                        <h3 class="text-gray-400 text-sm">Event Date</h3>
                    </div>
                </div>
                
                <!-- Event Banner -->
                @if($event->banner)
                    <div class="glass-card rounded-2xl overflow-hidden mb-8">
                        <img src="{{ asset('storage/' . $event->banner) }}" alt="{{ $event->title }}" class="w-full h-64 object-cover">
                    </div>
                @endif
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="glass-card rounded-2xl p-6">
                            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                                <i data-lucide="info" class="w-5 h-5 text-[#ff2d55]"></i>
                                Event Description
                            </h2>
                            <p class="text-gray-300 leading-relaxed">{{ $event->description }}</p>
                        </div>
                        
                        <div class="glass-card rounded-2xl p-6">
                            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                                <i data-lucide="ticket" class="w-5 h-5 text-[#ff2d55]"></i>
                                Ticket Types
                            </h2>
                            
                            <div class="space-y-4">
                                @forelse($tickets as $ticket)
                                    @php
                                        $sold = \App\Models\Eticket::where('ticket_id', $ticket->id)->count();
                                        $percentage = $ticket->stock > 0 ? ($sold / $ticket->stock) * 100 : 0;
                                    @endphp
                                    <div class="p-4 rounded-xl bg-white/5">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <h3 class="font-semibold">{{ $ticket->name }}</h3>
                                                <p class="text-xs text-gray-500">{{ $ticket->description ?? 'No description' }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-green-400">Rp {{ number_format($ticket->price, 0, ',', '.') }}</p>
                                                <p class="text-xs text-gray-500">Stock: {{ $ticket->stock }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <div class="flex justify-between text-xs text-gray-400 mb-1">
                                                <span>Sold: {{ $sold }}</span>
                                                <span>{{ number_format($percentage, 1) }}%</span>
                                            </div>
                                            <div class="w-full h-2 bg-white/10 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-gray-500 py-4">No tickets created yet</p>
                                @endforelse
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-white/10">
                                <a href="{{ route('organizer.event.tickets', $event->id) }}" class="inline-flex items-center gap-2 text-[#ff2d55] hover:text-white transition-colors">
                                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                    Manage Tickets
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="glass-card rounded-2xl p-6">
                            <h3 class="font-semibold mb-4 flex items-center gap-2">
                                <i data-lucide="map-pin" class="w-4 h-4 text-[#ff2d55]"></i>
                                Location
                            </h3>
                            <p class="text-gray-300">{{ $event->location }}</p>
                        </div>
                        
                        <div class="glass-card rounded-2xl p-6">
                            <h3 class="font-semibold mb-4 flex items-center gap-2">
                                <i data-lucide="clock" class="w-4 h-4 text-[#ff2d55]"></i>
                                Event Schedule
                            </h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Start Date:</span>
                                    <span>{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y, H:i') }} WIB</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">End Date:</span>
                                    <span>{{ \Carbon\Carbon::parse($event->end_date)->format('d M Y, H:i') }} WIB</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Status:</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs {{ $event->status == 'published' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}">
                                        {{ $event->status == 'published' ? 'Published' : 'Draft' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="glass-card rounded-2xl p-6">
                            <h3 class="font-semibold mb-4 flex items-center gap-2">
                                <i data-lucide="settings" class="w-4 h-4 text-[#ff2d55]"></i>
                                Actions
                            </h3>
                            <div class="space-y-2">
                                <a href="{{ route('organizer.event.edit', $event->id) }}" class="flex items-center gap-2 w-full px-4 py-2 bg-white/10 hover:bg-yellow-500/20 rounded-lg transition-colors">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    Edit Event
                                </a>
                                <a href="{{ route('organizer.event.tickets', $event->id) }}" class="flex items-center gap-2 w-full px-4 py-2 bg-white/10 hover:bg-purple-500/20 rounded-lg transition-colors">
                                    <i data-lucide="ticket" class="w-4 h-4"></i>
                                    Manage Tickets
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        lucide.createIcons();
        
        // Dropdown toggle untuk organizer
        const organizerMenuBtn = document.getElementById('organizerMenuBtn');
        const organizerDropdown = document.getElementById('organizerDropdown');
        
        if (organizerMenuBtn) {
            organizerMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                organizerDropdown.classList.toggle('hidden');
            });
        }
        
        document.addEventListener('click', function() {
            if (organizerDropdown) {
                organizerDropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>