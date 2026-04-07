<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard - Tixflix</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b0b10; color: #ffffff; }
        .glass-panel { background: rgba(18, 18, 24, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-card { background: linear-gradient(145deg, rgba(30, 30, 40, 0.8) 0%, rgba(15, 15, 20, 0.9) 100%); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3); backdrop-filter: blur(8px); }
        .text-gradient { background: linear-gradient(135deg, #ff2d55 0%, #ff5e3a 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .sidebar-item { transition: all 0.3s ease; }
        .sidebar-item:hover { background: rgba(255, 45, 85, 0.1); transform: translateX(5px); }
        .sidebar-item.active { background: linear-gradient(90deg, rgba(255, 45, 85, 0.2) 0%, rgba(255, 94, 58, 0.1) 100%); border-left: 3px solid #ff2d55; }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-5px); }
        .btn-export { transition: all 0.3s ease; }
        .btn-export:hover { transform: scale(1.05); }
        
        /* CSS for Print */
        @media print {
            .sidebar, .no-print, .btn-export, .stat-card, .glass-panel, .glass-card {
                display: none !important;
            }
            .fixed.inset-0, .pointer-events-none, .-z-10 {
                display: none !important;
            }
            .flex.h-screen {
                display: block !important;
            }
            .flex-1.ml-72 {
                margin-left: 0 !important;
                width: 100% !important;
            }
            main {
                padding: 0 !important;
            }
            table {
                border-collapse: collapse !important;
                width: 100% !important;
            }
            th, td {
                border: 1px solid #000 !important;
                padding: 8px !important;
                text-align: left !important;
            }
            th {
                background-color: #f0f0f0 !important;
                font-weight: bold !important;
            }
            .glass-card {
                background: white !important;
                border: 1px solid #ddd !important;
                box-shadow: none !important;
                margin-bottom: 20px !important;
                page-break-inside: avoid !important;
            }
            body, .text-gray-100, .text-gray-400, .text-gray-300 {
                color: black !important;
            }
            .text-gradient, .text-transparent {
                color: black !important;
                background: none !important;
                -webkit-text-fill-color: black !important;
            }
            canvas {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 2px solid #000;
            }
            .print-footer {
                display: block !important;
                text-align: center;
                margin-top: 20px;
                padding-top: 10px;
                border-top: 1px solid #ddd;
                font-size: 10px;
            }
        }
        
        .print-header, .print-footer {
            display: none;
        }
        
        /* Dropdown styles */
        .dropdown-menu {
            position: absolute;
            bottom: 100%;
            left: 0;
            margin-bottom: 8px;
            min-width: 220px;
            z-index: 100;
        }
        
        /* Notification dropdown */
        .notification-dropdown {
            position: absolute;
            left: 0;
            top: 100%;
            margin-top: 8px;
            width: 380px;
            max-width: calc(100vw - 20px);
            z-index: 100;
        }
        
        /* Word wrap untuk teks notifikasi */
        .notification-item {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
        }
        
        .notification-message {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
            line-height: 1.4;
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
        <aside class="sidebar w-72 glass-panel border-r border-white/10 flex flex-col fixed h-full overflow-y-auto">
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
                <a href="{{ route('organizer.dashboard') }}" class="sidebar-item {{ request()->routeIs('organizer.dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('organizer.events') }}" class="sidebar-item {{ request()->routeIs('organizer.events') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
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
                
                <a href="{{ route('organizer.waitinglist') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
                    <i data-lucide="list" class="w-5 h-5"></i>
                    <span>Waiting List</span>
                    @php
                        $waitingCount = \App\Models\WaitingList::whereHas('ticket.event', function($q) {
                            $q->where('user_id', auth()->id());
                        })->where('status', 'waiting')->count();
                    @endphp
                    @if($waitingCount > 0)
                        <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $waitingCount }}</span>
                    @endif
                </a>
            </nav>
            
            <!-- NOTIFICATION SECTION -->
            <div class="px-4 py-2 border-b border-white/10">
                <div class="relative">
                    <button id="notificationBtn" class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-white/5 transition-colors">
                        <div class="relative">
                            <i data-lucide="bell" class="w-5 h-5 text-gray-400"></i>
                            <span id="notificationBadge" class="absolute -top-2 -right-2 w-4 h-4 bg-[#ff2d55] text-white text-[10px] rounded-full flex items-center justify-center hidden">
                                0
                            </span>
                        </div>
                        <span class="text-sm text-gray-300">Notifikasi</span>
                    </button>
                    
                    <div id="notificationDropdown" class="notification-dropdown glass-card rounded-xl overflow-hidden hidden z-50" style="width: 380px;">
                        <div class="p-3 border-b border-white/10 flex justify-between items-center">
                            <h3 class="text-sm font-semibold">Notifikasi</h3>
                            <button id="markAllReadBtn" class="text-xs text-[#ff2d55] hover:text-white transition-colors">
                                Tandai semua
                            </button>
                        </div>
                        <div id="notificationList" class="max-h-96 overflow-y-auto">
                            <div class="p-4 text-center text-gray-500 text-sm">
                                <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2"></i>
                                <p>Belum ada notifikasi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
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
                    
                    <div id="organizerDropdown" class="dropdown-menu glass-card rounded-xl overflow-hidden hidden">
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
                @if(session('success'))
                    <div class="mb-6 bg-green-500/10 border-l-4 border-green-500 text-green-500 p-4 rounded-r-lg no-print">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 bg-red-500/10 border-l-4 border-red-500 text-red-500 p-4 rounded-r-lg no-print">
                        {{ session('error') }}
                    </div>
                @endif
                
                <!-- Print Header -->
                <div class="print-header">
                    <h1 style="font-size: 24px; margin-bottom: 5px;">Tixflix Financial Report</h1>
                    <p>{{ auth()->user()->name }} | {{ auth()->user()->email }}</p>
                    <p>Generated: {{ now()->format('d F Y H:i') }}</p>
                </div>
                
                <!-- Header with Export Buttons -->
                <div class="flex justify-between items-center mb-6 no-print">
                    <div>
                        <h1 class="text-3xl font-bold">Welcome back, {{ auth()->user()->name }}!</h1>
                        <p class="text-gray-400 mt-1">Here's your financial overview</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('organizer.report.export.pdf') }}" target="_blank"
                            class="btn-export px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 rounded-xl font-semibold hover:shadow-lg transition-all flex items-center gap-2">
                            <i data-lucide="file-pdf" class="w-4 h-4"></i>
                            Export PDF Report
                        </a>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="glass-card rounded-2xl p-6 stat-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center">
                                <i data-lucide="calendar" class="w-6 h-6 text-blue-400"></i>
                            </div>
                            <span class="text-2xl font-bold">{{ $totalEvents ?? 0 }}</span>
                        </div>
                        <h3 class="text-gray-400 text-sm">Total Events</h3>
                        <p class="text-xs text-gray-500 mt-1">All your events</p>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-6 stat-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-full bg-green-500/20 flex items-center justify-center">
                                <i data-lucide="ticket" class="w-6 h-6 text-green-400"></i>
                            </div>
                            <span class="text-2xl font-bold">{{ number_format($totalTicketsSold ?? 0) }}</span>
                        </div>
                        <h3 class="text-gray-400 text-sm">Tickets Sold</h3>
                        <p class="text-xs text-gray-500 mt-1">Total tickets sold</p>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-6 stat-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-full bg-yellow-500/20 flex items-center justify-center">
                                <i data-lucide="wallet" class="w-6 h-6 text-yellow-400"></i>
                            </div>
                            <span class="text-2xl font-bold">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <h3 class="text-gray-400 text-sm">Total Revenue</h3>
                        <p class="text-xs text-gray-500 mt-1">From ticket sales</p>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-6 stat-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-full bg-purple-500/20 flex items-center justify-center">
                                <i data-lucide="calendar-check" class="w-6 h-6 text-purple-400"></i>
                            </div>
                            <span class="text-2xl font-bold">{{ $upcomingEvents ?? 0 }}</span>
                        </div>
                        <h3 class="text-gray-400 text-sm">Upcoming Events</h3>
                        <p class="text-xs text-gray-500 mt-1">Events this month</p>
                    </div>
                </div>
                
                <!-- Financial Summary Cards -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="glass-card rounded-2xl p-6">
                        <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                            <i data-lucide="trending-up" class="w-5 h-5 text-green-400"></i>
                            Financial Summary
                        </h2>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-white/10">
                                <span class="text-gray-400">Gross Revenue</span>
                                <span class="text-xl font-bold text-green-400">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-white/10">
                                <span class="text-gray-400">Platform Fee (10%)</span>
                                <span class="text-xl font-bold text-yellow-400">Rp {{ number_format(($totalRevenue ?? 0) * 0.1, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-white/10">
                                <span class="text-gray-400">Net Revenue</span>
                                <span class="text-xl font-bold text-blue-400">Rp {{ number_format(($totalRevenue ?? 0) * 0.9, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-400">Average Ticket Price</span>
                                <span class="text-xl font-bold text-white">Rp {{ number_format(($totalTicketsSold ?? 0) > 0 ? ($totalRevenue ?? 0) / ($totalTicketsSold ?? 0) : 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-6">
                        <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                            <i data-lucide="pie-chart" class="w-5 h-5 text-purple-400"></i>
                            Sales by Event
                        </h2>
                        <canvas id="salesChart" class="w-full h-64"></canvas>
                    </div>
                </div>
                
                <!-- Events Financial Report Table -->
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6 no-print">
                        <h2 class="text-xl font-bold flex items-center gap-2">
                            <i data-lucide="calendar" class="w-5 h-5 text-[#ff2d55]"></i>
                            Events Financial Report
                        </h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-white/10">
                                <tr class="text-left text-gray-400 text-sm">
                                    <th class="px-4 py-3">Event</th>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Tickets Sold</th>
                                    <th class="px-4 py-3">Revenue</th>
                                    <th class="px-4 py-3">Platform Fee</th>
                                    <th class="px-4 py-3">Net Income</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @forelse($eventFinancials ?? [] as $event)
                                    <tr class="border-b border-white/5">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                @if($event['banner'])
                                                    <img src="{{ asset('storage/' . $event['banner']) }}" alt="{{ $event['title'] }}" class="w-10 h-10 rounded-lg object-cover no-print">
                                                @endif
                                                <div>
                                                    <p class="font-semibold">{{ $event['title'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ $event['category'] }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($event['start_date'])->format('d M Y') }}</td>
                                        <td class="px-4 py-3 text-sm font-semibold">{{ number_format($event['tickets_sold']) }}</td>
                                        <td class="px-4 py-3 text-sm text-green-400">Rp {{ number_format($event['revenue'], 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm text-yellow-400">Rp {{ number_format($event['platform_fee'], 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm text-blue-400 font-semibold">Rp {{ number_format($event['net_income'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            <i data-lucide="calendar-x" class="w-12 h-12 mx-auto mb-3"></i>
                                            <p>No events created yet</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(isset($eventFinancials) && count($eventFinancials) > 0)
                            <tfoot class="border-t border-white/10">
                                <tr class="font-bold">
                                    <td class="px-4 py-3">Total</td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3">{{ number_format(collect($eventFinancials)->sum('tickets_sold')) }}</td>
                                    <td class="px-4 py-3 text-green-400">Rp {{ number_format(collect($eventFinancials)->sum('revenue'), 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-yellow-400">Rp {{ number_format(collect($eventFinancials)->sum('platform_fee'), 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-blue-400">Rp {{ number_format(collect($eventFinancials)->sum('net_income'), 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
                
                <!-- Print Footer -->
                <div class="print-footer">
                    <p>This report is generated automatically by Tixflix System</p>
                    <p>&copy; {{ date('Y') }} Tixflix - All Rights Reserved</p>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        lucide.createIcons();
        
        // ========================================
        // PROFILE DROPDOWN FOR ORGANIZER
        // ========================================
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
        
        // ========================================
        // NOTIFICATION SYSTEM
        // ========================================
        let notificationBtn = document.getElementById('notificationBtn');
        let notificationDropdown = document.getElementById('notificationDropdown');
        let notificationList = document.getElementById('notificationList');
        let notificationBadge = document.getElementById('notificationBadge');

        function loadNotifications() {
            fetch('{{ route("notifications.get") }}')
                .then(response => response.json())
                .then(data => {
                    updateNotificationBadge(data.unread_count);
                    renderNotifications(data.notifications);
                })
                .catch(error => console.error('Error loading notifications:', error));
        }

        function updateNotificationBadge(count) {
            if (count > 0) {
                notificationBadge.textContent = count > 9 ? '9+' : count;
                notificationBadge.classList.remove('hidden');
            } else {
                notificationBadge.classList.add('hidden');
            }
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000);
            
            if (diff < 60) return 'Baru saja';
            if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
            if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
            if (diff < 604800) return Math.floor(diff / 86400) + ' hari lalu';
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        }

        function renderNotifications(notifications) {
            if (!notifications || notifications.length === 0) {
                notificationList.innerHTML = `
                    <div class="p-6 text-center text-gray-500">
                        <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2"></i>
                        <p class="text-sm">Tidak ada notifikasi</p>
                    </div>
                `;
                lucide.createIcons();
                return;
            }
            
            let html = '';
            notifications.forEach(notif => {
                const iconColor = notif.type === 'success' ? 'text-green-400' : 
                                 (notif.type === 'warning' ? 'text-yellow-400' : 'text-blue-400');
                const iconName = notif.type === 'success' ? 'check-circle' : 
                                (notif.type === 'warning' ? 'alert-triangle' : 'bell');
                
                html += `
                    <div class="notification-item p-3 hover:bg-white/5 transition-colors border-b border-white/5 cursor-pointer ${!notif.is_read ? 'bg-[#ff2d55]/5' : ''}" 
                         data-id="${notif.id}" 
                         data-link="${notif.link || '#'}">
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="${iconName}" class="w-4 h-4 ${iconColor}"></i>
                            </div>
                            <div class="flex-1 min-w-0" style="word-wrap: break-word; word-break: break-word;">
                                <p class="text-sm font-semibold" style="word-wrap: break-word;">${escapeHtml(notif.title)}</p>
                                <p class="text-xs text-gray-400 mt-0.5 notification-message" style="word-wrap: break-word; white-space: normal;">${escapeHtml(notif.message)}</p>
                                <p class="text-xs text-gray-500 mt-1">${formatDate(notif.created_at)}</p>
                            </div>
                            <button class="delete-notif text-gray-500 hover:text-red-400 transition ml-2" data-id="${notif.id}">
                                <i data-lucide="x" class="w-3 h-3"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            notificationList.innerHTML = html;
            lucide.createIcons();
            
            document.querySelectorAll('.notification-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    if (e.target.closest('.delete-notif')) return;
                    const id = this.dataset.id;
                    const link = this.dataset.link;
                    
                    fetch(`/notifications/${id}/read`, { 
                        method: 'POST', 
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } 
                    }).then(() => {
                        if (link && link !== '#') {
                            window.location.href = link;
                        } else {
                            loadNotifications();
                        }
                    });
                });
            });
            
            document.querySelectorAll('.delete-notif').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const id = this.dataset.id;
                    if (confirm('Hapus notifikasi ini?')) {
                        fetch(`/notifications/${id}`, { 
                            method: 'DELETE', 
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } 
                        }).then(() => loadNotifications());
                    }
                });
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        if (notificationBtn) {
            notificationBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
                if (!notificationDropdown.classList.contains('hidden')) {
                    loadNotifications();
                }
            });
        }

        document.getElementById('markAllReadBtn')?.addEventListener('click', function() {
            fetch('{{ route("notifications.readAll") }}', { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } 
            }).then(() => loadNotifications());
        });

        document.addEventListener('click', function(e) {
            if (notificationDropdown && !notificationBtn?.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
        });

        setTimeout(() => {
            loadNotifications();
        }, 1000);
        
        // ========================================
        // CHART.JS FOR SALES CHART
        // ========================================
        @if(isset($eventSalesData) && count($eventSalesData) > 0)
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($eventLabels ?? []),
                datasets: [{
                    label: 'Tickets Sold',
                    data: @json($eventSalesData ?? []),
                    backgroundColor: 'rgba(255, 45, 85, 0.5)',
                    borderColor: '#ff2d55',
                    borderWidth: 2,
                    borderRadius: 8
                }, {
                    label: 'Revenue (Rp)',
                    data: @json($eventRevenueData ?? []),
                    backgroundColor: 'rgba(89, 70, 234, 0.5)',
                    borderColor: '#5946ea',
                    borderWidth: 2,
                    borderRadius: 8,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { color: '#fff' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Tickets Sold',
                            color: '#fff'
                        },
                        ticks: { color: '#fff' },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    },
                    y1: {
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Revenue (Rp)',
                            color: '#fff'
                        },
                        ticks: { 
                            color: '#fff',
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        },
                        grid: { display: false }
                    },
                    x: {
                        ticks: { color: '#fff' },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    }
                }
            }
        });
        @endif
    </script>
</body>
</html>