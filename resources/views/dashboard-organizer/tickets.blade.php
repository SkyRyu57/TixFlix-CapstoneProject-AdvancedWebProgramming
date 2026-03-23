<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tickets - {{ $event->title }}</title>
    
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
        input, select, textarea { transition: all 0.3s ease; }
        input:focus, select:focus, textarea:focus { border-color: #ff2d55; outline: none; box-shadow: 0 0 0 2px rgba(255, 45, 85, 0.2); }
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px); z-index: 1000; display: none; align-items: center; justify-content: center; }
        .modal-content { background: linear-gradient(145deg, rgba(30, 30, 40, 0.95) 0%, rgba(15, 15, 20, 0.98) 100%); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 24px; padding: 24px; max-width: 500px; width: 90%; position: relative; }
    </style>
</head>
<body class="bg-[#0b0b0f] text-gray-100">
    
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-[#ff2d55]/10 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] rounded-full bg-[#6a5af9]/10 blur-[120px]"></div>
    </div>
    
    <!-- Modal for Add/Edit Ticket -->
    <div id="ticketModal" class="modal-overlay" onclick="closeTicketModal(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold" id="modalTitle">Add New Ticket</h3>
                <button onclick="closeTicketModal()" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form id="ticketForm" method="POST">
                @csrf
                <div id="methodField"></div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Ticket Name *</label>
                        <input type="text" name="name" id="ticketName" required 
                               class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors"
                               placeholder="e.g., VIP, Regular, Early Bird">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-2">Price (Rp) *</label>
                        <input type="number" name="price" id="ticketPrice" required min="0"
                               class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors"
                               placeholder="0">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-2">Stock *</label>
                        <input type="number" name="stock" id="ticketStock" required min="1"
                               class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors"
                               placeholder="1">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-2">Description</label>
                        <textarea name="description" id="ticketDescription" rows="3"
                                  class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors"
                                  placeholder="Describe the ticket benefits..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeTicketModal()" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] rounded-lg font-semibold hover:shadow-lg transition-all">
                        <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                        Save Ticket
                    </button>
                </div>
            </form>
        </div>
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
                <div class="flex items-center gap-3 mb-6">
                    <a href="{{ route('organizer.event.detail', $event->id) }}" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold">Manage Tickets</h1>
                        <p class="text-gray-400 mt-1">{{ $event->title }}</p>
                    </div>
                </div>
                
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
                
                <!-- Add Ticket Button -->
                <div class="mb-6">
                    <button onclick="openAddTicketModal()" class="px-4 py-2 bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] rounded-xl font-semibold hover:shadow-lg transition-all">
                        <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
                        Add New Ticket
                    </button>
                </div>
                
                <!-- Tickets List -->
                <div class="glass-card rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-white/10">
                                <tr class="text-left text-gray-400 text-sm">
                                    <th class="px-6 py-4">Ticket Name</th>
                                    <th class="px-6 py-4">Price</th>
                                    <th class="px-6 py-4">Stock</th>
                                    <th class="px-6 py-4">Sold</th>
                                    <th class="px-6 py-4">Available</th>
                                    <th class="px-6 py-4">Actions</th>
                                 </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    @php
                                        $soldCount = \App\Models\Eticket::where('ticket_id', $ticket->id)->count();
                                        $available = $ticket->stock - $soldCount;
                                    @endphp
                                    <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="font-semibold">{{ $ticket->name }}</p>
                                                @if($ticket->description)
                                                    <p class="text-xs text-gray-500">{{ Str::limit($ticket->description, 50) }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-lg font-bold text-green-400">
                                            Rp {{ number_format($ticket->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ number_format($ticket->stock) }}
                                        </td>
                                        <td class="px-6 py-4 text-yellow-400">
                                            {{ number_format($soldCount) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded-full text-xs {{ $available > 0 ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                                {{ number_format($available) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex gap-2">
                                                <button onclick='openEditTicketModal({{ $ticket->id }}, "{{ addslashes($ticket->name) }}", {{ $ticket->price }}, {{ $ticket->stock }}, "{{ addslashes($ticket->description) }}")' 
                                                        class="p-2 bg-white/10 hover:bg-yellow-500/20 rounded-lg transition-colors" title="Edit">
                                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                                </button>
                                                <form action="/organizer/events/{{ $event->id }}/tickets/{{ $ticket->id }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Are you sure? This will delete this ticket type.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 bg-white/10 hover:bg-red-500/20 rounded-lg transition-colors" title="Delete" {{ $soldCount > 0 ? 'disabled' : '' }}>
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <i data-lucide="ticket" class="w-12 h-12 mx-auto mb-3"></i>
                                            <p>No tickets created yet</p>
                                            <button onclick="openAddTicketModal()" class="inline-block mt-3 text-[#ff2d55] hover:text-white">
                                                Add your first ticket →
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
                    <div class="glass-card rounded-2xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                                <i data-lucide="ticket" class="w-5 h-5 text-green-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Total Tickets</p>
                                <p class="text-xl font-bold">{{ number_format($tickets->sum('stock')) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                                <i data-lucide="trending-up" class="w-5 h-5 text-yellow-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Total Sold</p>
                                <p class="text-xl font-bold">{{ number_format($tickets->sum(function($t) { return \App\Models\Eticket::where('ticket_id', $t->id)->count(); })) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                                <i data-lucide="wallet" class="w-5 h-5 text-purple-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Total Revenue</p>
                                <p class="text-xl font-bold">Rp {{ number_format($tickets->sum(function($t) { 
                                    return \App\Models\Eticket::where('ticket_id', $t->id)->count() * $t->price; 
                                }), 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                                <i data-lucide="percent" class="w-5 h-5 text-blue-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Sold Percentage</p>
                                @php
                                    $totalStock = $tickets->sum('stock');
                                    $totalSold = $tickets->sum(function($t) { return \App\Models\Eticket::where('ticket_id', $t->id)->count(); });
                                    $percentage = $totalStock > 0 ? ($totalSold / $totalStock) * 100 : 0;
                                @endphp
                                <p class="text-xl font-bold">{{ number_format($percentage, 1) }}%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        lucide.createIcons();
        
        function openAddTicketModal() {
            document.getElementById('modalTitle').innerText = 'Add New Ticket';
            document.getElementById('ticketForm').action = "/organizer/events/{{ $event->id }}/tickets";
            document.getElementById('ticketForm').method = 'POST';
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('ticketName').value = '';
            document.getElementById('ticketPrice').value = '';
            document.getElementById('ticketStock').value = '';
            document.getElementById('ticketDescription').value = '';
            document.getElementById('ticketModal').style.display = 'flex';
            lucide.createIcons();
        }
        
        function openEditTicketModal(id, name, price, stock, description) {
            document.getElementById('modalTitle').innerText = 'Edit Ticket';
            document.getElementById('ticketForm').action = "/organizer/events/{{ $event->id }}/tickets/" + id;
            document.getElementById('ticketForm').method = 'POST';
            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('ticketName').value = name;
            document.getElementById('ticketPrice').value = price;
            document.getElementById('ticketStock').value = stock;
            document.getElementById('ticketDescription').value = description;
            document.getElementById('ticketModal').style.display = 'flex';
            lucide.createIcons();
        }
        
        function closeTicketModal(event) {
            if (event && event.target === document.getElementById('ticketModal')) {
                document.getElementById('ticketModal').style.display = 'none';
            } else if (!event) {
                document.getElementById('ticketModal').style.display = 'none';
            }
        }
        
        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTicketModal();
            }
        });
    </script>
</body>
</html>