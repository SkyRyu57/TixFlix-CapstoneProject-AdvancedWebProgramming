<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendees - Organizer Dashboard</title>
    
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
        .status-badge { transition: all 0.3s ease; }
        .status-badge:hover { transform: scale(1.05); }
        
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
                
                <a href="{{ route('organizer.events') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    <span>Events</span>
                </a>
                
                <a href="{{ route('organizer.event.create') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    <span>Create Event</span>
                </a>
                
                <a href="{{ route('organizer.attendees') }}" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
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
        
        <!-- Main Content (sama seperti sebelumnya) -->
        <main class="flex-1 ml-72 overflow-y-auto">
            <div class="p-8">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold">Attendees</h1>
                    <p class="text-gray-400 mt-1">People who bought tickets for your events</p>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="glass-card rounded-2xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                                <i data-lucide="users" class="w-5 h-5 text-blue-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Total Attendees</p>
                                <p class="text-xl font-bold">{{ number_format($attendees->total()) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                                <i data-lucide="ticket" class="w-5 h-5 text-green-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Total Tickets</p>
                                <p class="text-xl font-bold">{{ number_format(\App\Models\Eticket::whereHas('ticket.event', function($q) { $q->where('user_id', auth()->id()); })->count()) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                                <i data-lucide="calendar" class="w-5 h-5 text-purple-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Events</p>
                                <p class="text-xl font-bold">{{ number_format(\App\Models\Event::where('user_id', auth()->id())->count()) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                                <i data-lucide="trending-up" class="w-5 h-5 text-yellow-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Conversion Rate</p>
                                <p class="text-xl font-bold">-</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Attendees List -->
                <div class="glass-card rounded-2xl overflow-hidden">
                    <div class="p-4 border-b border-white/10">
                        <div class="flex items-center gap-2">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                            <input type="text" placeholder="Search attendees by name or ticket code..." 
                                   class="flex-1 bg-transparent border-none focus:outline-none text-sm"
                                   id="searchInput">
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-white/10">
                                <tr class="text-left text-gray-400 text-sm">
                                    <th class="px-6 py-4">Ticket Code</th>
                                    <th class="px-6 py-4">Attendee</th>
                                    <th class="px-6 py-4">Event</th>
                                    <th class="px-6 py-4">Ticket Type</th>
                                    <th class="px-6 py-4">Purchase Date</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="attendeesTable">
                                @forelse($attendees as $attendee)
                                    <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4">
                                            <code class="text-xs bg-white/10 px-2 py-1 rounded">{{ $attendee->ticket_code }}</code>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center text-xs font-bold">
                                                    {{ substr($attendee->user->name ?? 'N/A', 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-sm">{{ $attendee->user->name ?? 'Unknown' }}</p>
                                                    <p class="text-xs text-gray-500">{{ $attendee->user->email ?? 'No email' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="font-medium text-sm">{{ $attendee->ticket->event->title ?? 'N/A' }}</p>
                                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($attendee->ticket->event->start_date ?? now())->format('d M Y') }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm">{{ $attendee->ticket->name ?? 'N/A' }}</span>
                                            <p class="text-xs text-green-400">Rp {{ number_format($attendee->ticket->price ?? 0, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            {{ \Carbon\Carbon::parse($attendee->created_at)->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="status-badge px-2 py-1 rounded-full text-xs {{ $attendee->is_scanned ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400' }}">
                                                {{ $attendee->is_scanned ? 'Scanned' : 'Valid' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button onclick="showTicketDetail('{{ $attendee->ticket_code }}', '{{ addslashes($attendee->user->name ?? 'Unknown') }}', '{{ $attendee->ticket->event->title ?? 'N/A' }}', '{{ $attendee->ticket->name ?? 'N/A' }}', {{ $attendee->is_scanned ? 'true' : 'false' }})" 
                                                    class="p-2 bg-white/10 hover:bg-blue-500/20 rounded-lg transition-colors" title="View Details">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                            @if(!$attendee->is_scanned)
                                                <button onclick="markAsScanned('{{ $attendee->ticket_code }}')" 
                                                        class="p-2 bg-white/10 hover:bg-green-500/20 rounded-lg transition-colors" title="Mark as Scanned">
                                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                </button>
                                            @endif
                                         </td>
                                     </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                            <i data-lucide="users" class="w-12 h-12 mx-auto mb-3"></i>
                                            <p>No attendees yet</p>
                                            <p class="text-sm mt-1">When people buy tickets, they will appear here</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-white/10">
                        {{ $attendees->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Modal for Ticket Detail -->
    <div id="ticketDetailModal" class="modal-overlay" onclick="closeTicketDetailModal(event)">
        <div class="modal-content" onclick="event.stopPropagation()" style="max-width: 400px;">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Ticket Details</h3>
                <button onclick="closeTicketDetailModal()" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <div class="text-center">
                    <div class="w-24 h-24 mx-auto mb-3 rounded-full bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center">
                        <i data-lucide="ticket" class="w-12 h-12 text-white"></i>
                    </div>
                    <code class="text-xs bg-white/10 px-3 py-1 rounded" id="detailTicketCode">-</code>
                </div>
                
                <div class="border-t border-white/10 pt-4">
                    <div class="flex justify-between py-2">
                        <span class="text-gray-400">Attendee:</span>
                        <span class="font-semibold" id="detailAttendeeName">-</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-400">Event:</span>
                        <span id="detailEventName">-</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-400">Ticket Type:</span>
                        <span id="detailTicketType">-</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-400">Status:</span>
                        <span id="detailStatus" class="px-2 py-0.5 rounded-full text-xs">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
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
            max-width: 500px;
            width: 90%;
            position: relative;
        }
    </style>
    
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
        
        function showTicketDetail(code, name, event, ticketType, isScanned) {
            document.getElementById('detailTicketCode').innerText = code;
            document.getElementById('detailAttendeeName').innerText = name;
            document.getElementById('detailEventName').innerText = event;
            document.getElementById('detailTicketType').innerText = ticketType;
            
            const statusSpan = document.getElementById('detailStatus');
            if (isScanned) {
                statusSpan.innerHTML = '<span class="bg-green-500/20 text-green-400 px-2 py-1 rounded-full">Scanned</span>';
            } else {
                statusSpan.innerHTML = '<span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded-full">Valid</span>';
            }
            
            document.getElementById('ticketDetailModal').style.display = 'flex';
            lucide.createIcons();
        }
        
        function closeTicketDetailModal(event) {
            if (event && event.target === document.getElementById('ticketDetailModal')) {
                document.getElementById('ticketDetailModal').style.display = 'none';
            } else if (!event) {
                document.getElementById('ticketDetailModal').style.display = 'none';
            }
        }
        
        function markAsScanned(ticketCode) {
            if (confirm(`Mark ticket ${ticketCode} as scanned?`)) {
                alert(`Ticket ${ticketCode} has been marked as scanned.`);
            }
        }
        
        // Search functionality
        document.getElementById('searchInput')?.addEventListener('keyup', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#attendeesTable tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTicketDetailModal();
            }
        });
    </script>
</body>
</html>