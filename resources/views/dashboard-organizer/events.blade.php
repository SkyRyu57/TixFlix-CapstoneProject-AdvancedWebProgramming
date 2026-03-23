<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events - Organizer Dashboard</title>
    
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
            
            <div class="p-4 border-t border-white/10">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center font-bold">
                        {{ substr(auth()->user()->name ?? 'O', 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                            <i data-lucide="log-out" class="w-5 h-5"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 ml-72 overflow-y-auto">
            <div class="p-8">
                @if(session('success'))
                    <div class="mb-6 bg-green-500/10 border-l-4 border-green-500 text-green-500 p-4 rounded-r-lg">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 bg-red-500/10 border-l-4 border-red-500 text-red-500 p-4 rounded-r-lg">
                        {{ session('error') }}
                    </div>
                @endif
                
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold">My Events</h1>
                        <p class="text-gray-400 mt-1">Manage all your events here</p>
                    </div>
                    <a href="{{ route('organizer.event.create') }}" class="px-4 py-2 bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] rounded-xl font-semibold hover:shadow-lg transition-all">
                        <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
                        Create Event
                    </a>
                </div>
                
                <div class="glass-card rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-white/10">
                                <tr class="text-left text-gray-400 text-sm">
                                    <th class="px-6 py-4">Event</th>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4">Location</th>
                                    <th class="px-6 py-4">Tickets Sold</th>
                                    <th class="px-6 py-4">Revenue</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $event)
                                    @php
                                        $ticketsSold = \App\Models\Eticket::whereHas('ticket', function($q) use ($event) {
                                            $q->where('event_id', $event->id);
                                        })->count();
                                        
                                        $revenue = \App\Models\Eticket::whereHas('ticket', function($q) use ($event) {
                                            $q->where('event_id', $event->id);
                                        })->join('tickets', 'etickets.ticket_id', '=', 'tickets.id')
                                          ->sum('tickets.price');
                                    @endphp
                                    <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                @if($event->banner)
                                                    <img src="{{ asset('storage/' . $event->banner) }}" alt="{{ $event->title }}" class="w-10 h-10 rounded-lg object-cover">
                                                @else
                                                    <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
                                                        <i data-lucide="calendar" class="w-5 h-5 text-gray-500"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="font-semibold">{{ $event->title }}</p>
                                                    <p class="text-xs text-gray-500">{{ $event->category->name ?? 'Uncategorized' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-400">
                                            {{ Str::limit($event->location, 30) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold">
                                            {{ number_format($ticketsSold) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-green-400">
                                            Rp {{ number_format($revenue, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded-full text-xs {{ $event->status == 'published' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}">
                                                {{ $event->status == 'published' ? 'Published' : 'Draft' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex gap-2">
                                                <a href="{{ route('organizer.event.detail', $event->id) }}" class="p-2 bg-white/10 hover:bg-blue-500/20 rounded-lg transition-colors" title="View">
                                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                                </a>
                                                <a href="{{ route('organizer.event.edit', $event->id) }}" class="p-2 bg-white/10 hover:bg-yellow-500/20 rounded-lg transition-colors" title="Edit">
                                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                                </a>
                                                <a href="{{ route('organizer.event.tickets', $event->id) }}" class="p-2 bg-white/10 hover:bg-purple-500/20 rounded-lg transition-colors" title="Manage Tickets">
                                                    <i data-lucide="ticket" class="w-4 h-4"></i>
                                                </a>
                                                <form action="{{ route('organizer.event.delete', $event->id) }}" method="POST" onsubmit="return confirm('Are you sure? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 bg-white/10 hover:bg-red-500/20 rounded-lg transition-colors" title="Delete">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                            <i data-lucide="calendar-x" class="w-12 h-12 mx-auto mb-3"></i>
                                            <p>No events created yet</p>
                                            <a href="{{ route('organizer.event.create') }}" class="inline-block mt-3 text-[#ff2d55] hover:text-white">
                                                Create your first event →
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-white/10">
                        {{ $events->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>