<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Semua Event - Tixflix</title>

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
        
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 8px;
            min-width: 200px;
            z-index: 100;
        }
        
        .category-filter {
            transition: all 0.3s ease;
        }
        .category-filter:hover {
            transform: translateY(-2px);
        }
        .category-filter.active {
            background: linear-gradient(135deg, #ff2d55 0%, #ff5e3a 100%);
            color: white;
        }
        
        .filter-card {
            transition: all 0.3s ease;
        }
        .filter-card:hover {
            border-color: rgba(255, 45, 85, 0.3);
        }
        
        /* Loading animation */
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255, 45, 85, 0.3);
            border-top-color: #ff2d55;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Reset filter button */
        .reset-filter {
            transition: all 0.3s ease;
        }
        .reset-filter:hover {
            background: rgba(255, 45, 85, 0.2);
            color: #ff2d55;
        }
    </style>
</head>
<body class="bg-[#0b0b0f] text-gray-100 font-sans antialiased min-h-screen">

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
                        Beranda
                    </a>
                    <a href="{{ route('events.list') }}" class="text-white font-medium relative group">
                        Event
                        <span class="absolute -bottom-1.5 left-0 w-full h-0.5 bg-[#ff2d55] rounded-full"></span>
                    </a>
                    <a href="{{ route('my-tickets') }}" class="text-gray-400 hover:text-white font-medium transition-colors">
                        Tiket Saya
                    </a>
                </div>

                <div class="flex items-center gap-2 md:gap-4">
                    <!-- Notifikasi Dropdown -->
                    <div class="relative">
                        <button id="notificationBtn" class="p-2 text-gray-400 hover:text-white transition-colors relative group">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            <span id="notificationBadge" class="absolute -top-1 -right-1 w-5 h-5 bg-[#ff2d55] text-white text-xs rounded-full flex items-center justify-center hidden">
                                0
                            </span>
                        </button>
                        
                        <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 glass-card rounded-xl overflow-hidden hidden z-50">
                            <div class="p-3 border-b border-white/10 flex justify-between items-center">
                                <h3 class="font-semibold">Notifikasi</h3>
                                <button id="markAllReadBtn" class="text-xs text-[#ff2d55] hover:text-white transition-colors">
                                    Tandai semua
                                </button>
                            </div>
                            <div id="notificationList" class="max-h-96 overflow-y-auto">
                                <div class="p-4 text-center text-gray-500">
                                    <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2"></i>
                                    <p class="text-sm">Memuat notifikasi...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <button id="userMenuBtn" class="focus:outline-none group">
                            <div class="relative flex items-center gap-3 md:pl-4 md:border-l border-white/10">
                                <div class="hidden md:block text-right">
                                    <div class="text-sm font-semibold group-hover:text-[#ff2d55] transition-colors">{{ auth()->user()->name ?? 'Tamu' }}</div>
                                    <div class="text-xs text-gray-400">Penjelajah Event</div>
                                </div>
                                <div class="w-10 h-10 rounded-full border-2 border-[#1e1e28] bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center text-white font-bold hover:border-[#ff2d55] transition-colors shadow-lg shadow-[#ff2d55]/20">
                                    {{ substr(auth()->user()->name ?? 'G', 0, 1) }}
                                </div>
                            </div>
                        </button>
                        
                        <div id="userDropdown" class="dropdown-menu glass-card rounded-xl overflow-hidden hidden">
                            <div class="p-3 border-b border-white/10">
                                <p class="font-semibold text-sm">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition-colors">
                                <i data-lucide="user" class="w-4 h-4"></i>
                                <span class="text-sm">Profil Saya</span>
                            </a>
                            <a href="{{ route('my-tickets') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition-colors">
                                <i data-lucide="ticket" class="w-4 h-4"></i>
                                <span class="text-sm">Tiket Saya</span>
                            </a>
                            <div class="border-t border-white/10"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition-colors w-full text-left">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span class="text-sm">Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">Semua Event</h1>
            <p class="text-gray-400">Temukan event menarik yang sesuai dengan minatmu</p>
        </div>

        <!-- Filter Section -->
        <div class="glass-card rounded-2xl p-6 mb-8 filter-card">
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Search Bar -->
                <div class="flex-1">
                    <label class="block text-sm font-medium mb-2 text-gray-400">
                        <i data-lucide="search" class="w-4 h-4 inline mr-1"></i>
                        Cari Event
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="searchInput" 
                               placeholder="Cari berdasarkan nama event..." 
                               value="{{ request('search', '') }}"
                               class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors pl-11">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500"></i>
                    </div>
                </div>

                <!-- Filter by Location -->
                <div class="flex-1">
                    <label class="block text-sm font-medium mb-2 text-gray-400">
                        <i data-lucide="map-pin" class="w-4 h-4 inline mr-1"></i>
                        Lokasi
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="locationInput" 
                               placeholder="Filter berdasarkan lokasi (kota/venue)..." 
                               value="{{ request('location', '') }}"
                               class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors pl-11">
                        <i data-lucide="map-pin" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500"></i>
                    </div>
                </div>

                <!-- Reset Filter Button -->
                <div class="flex items-end">
                    <button id="resetFilterBtn" class="reset-filter px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-gray-400 hover:text-[#ff2d55] transition-all flex items-center gap-2">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Kategori -->
        <div class="mb-8">
            <div class="flex flex-wrap gap-2">
                <a href="?{{ http_build_query(array_merge(request()->except('category'), ['category' => ''])) }}" 
                   class="category-filter px-4 py-2 rounded-full text-sm font-medium transition-all {{ !request('category') ? 'bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] text-white' : 'bg-white/10 text-gray-300 hover:bg-white/20' }}">
                    Semua
                </a>
                @foreach($categories as $cat)
                    <a href="?{{ http_build_query(array_merge(request()->except('category'), ['category' => $cat->id])) }}" 
                       class="category-filter px-4 py-2 rounded-full text-sm font-medium transition-all {{ request('category') == $cat->id ? 'bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] text-white' : 'bg-white/10 text-gray-300 hover:bg-white/20' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="hidden text-center py-12">
            <div class="loading-spinner mx-auto mb-4"></div>
            <p class="text-gray-400">Memuat data...</p>
        </div>

        <!-- Results Info -->
        <div id="resultsInfo" class="mb-4 text-sm text-gray-400">
            Menampilkan <span id="showingStart">0</span> - <span id="showingEnd">0</span> dari <span id="totalResults">0</span> event
        </div>

        <!-- Grid Events -->
        <div id="eventsContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @if($events->count() > 0)
                @foreach($events as $event)
                    <div class="event-card" data-event-id="{{ $event->id }}">
                        <a href="{{ url('/events/' . $event->id) }}" 
                           class="glass-card rounded-2xl overflow-hidden group hover:-translate-y-1 transition-all duration-300 block">
                            
                            @if($event->banner)
                                <div class="aspect-video overflow-hidden">
                                    <img src="{{ Str::startsWith($event->banner, 'http') ? $event->banner : asset('storage/' . $event->banner) }}" 
                                         alt="{{ $event->title }}" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                </div>
                            @else
                                <div class="aspect-video bg-gradient-to-br from-[#6a5af9]/40 to-[#0b0b0f] flex items-center justify-center">
                                    <i data-lucide="calendar" class="w-12 h-12 text-white/20"></i>
                                </div>
                            @endif
                            
                            <div class="p-4">
                                <span class="text-xs font-bold text-[#6a5af9] uppercase">{{ $event->category->name ?? 'Event' }}</span>
                                <h3 class="font-bold text-lg mb-2 mt-1 group-hover:text-[#6a5af9] transition-colors line-clamp-2">
                                    {{ $event->title }}
                                </h3>
                                
                                <div class="space-y-2 text-sm text-gray-400 mb-4">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="calendar" class="w-4 h-4"></i>
                                        {{ \Carbon\Carbon::parse($event->start_date)->format('d M Y, H:i') }} WIB
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                                        {{ Str::limit($event->location, 30) }}
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-white">
                                        @if($event->tickets && $event->tickets->count() > 0)
                                            Rp {{ number_format($event->tickets->min('price'), 0, ',', '.') }}
                                        @else
                                            <span class="text-sm text-gray-500">Habis Terjual</span>
                                        @endif
                                    </span>
                                    
                                    <div class="bg-white/10 group-hover:bg-[#6a5af9] p-2 rounded-xl transition-colors">
                                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            @else
                <div class="col-span-full text-center py-16 glass-card rounded-3xl">
                    <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-white/5 flex items-center justify-center">
                        <i data-lucide="calendar-x" class="w-12 h-12 text-gray-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Belum Ada Event</h3>
                    <p class="text-gray-400 mb-6">Belum ada event yang tersedia saat ini</p>
                    <a href="{{ route('dashboard') }}" 
                       class="inline-block bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] hover:opacity-90 text-white px-6 py-3 rounded-xl font-bold transition-all">
                        Kembali ke Dashboard
                    </a>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        <div class="mt-8" id="paginationContainer">
            {{ $events->appends(request()->query())->links() }}
        </div>
    </main>

    <script>
        lucide.createIcons();

        // ========================================
        // DROPDOWN PROFIL
        // ========================================
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
        
        // ========================================
        // SISTEM NOTIFIKASI
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
                .catch(error => console.error('Error:', error));
        }

        function updateNotificationBadge(count) {
            if (count > 0) {
                notificationBadge.textContent = count > 9 ? '9+' : count;
                notificationBadge.classList.remove('hidden');
            } else {
                notificationBadge.classList.add('hidden');
            }
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
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold">${escapeHtml(notif.title)}</p>
                                <p class="text-xs text-gray-400 mt-1">${escapeHtml(notif.message)}</p>
                                <p class="text-xs text-gray-500 mt-1">${notif.time_ago}</p>
                            </div>
                            <button class="delete-notif text-gray-500 hover:text-red-400 transition" data-id="${notif.id}">
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

        document.addEventListener('click', function() {
            if (notificationDropdown) {
                notificationDropdown.classList.add('hidden');
            }
        });

        setTimeout(() => {
            loadNotifications();
        }, 1000);

        // ========================================
        // FILTER FUNCTIONALITY (AJAX)
        // ========================================
        
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const locationInput = document.getElementById('locationInput');
        const resetFilterBtn = document.getElementById('resetFilterBtn');
        const eventsContainer = document.getElementById('eventsContainer');
        const paginationContainer = document.getElementById('paginationContainer');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const resultsInfo = document.getElementById('resultsInfo');
        const showingStartSpan = document.getElementById('showingStart');
        const showingEndSpan = document.getElementById('showingEnd');
        const totalResultsSpan = document.getElementById('totalResults');
        
        let currentPage = 1;
        
        function fetchEvents() {
            const search = searchInput.value;
            const location = locationInput.value;
            const category = new URLSearchParams(window.location.search).get('category') || '';
            
            // Show loading
            loadingIndicator.classList.remove('hidden');
            eventsContainer.style.opacity = '0.5';
            
            // Build URL
            let url = '{{ route("events.list") }}?page=' + currentPage;
            if (search) url += '&search=' + encodeURIComponent(search);
            if (location) url += '&location=' + encodeURIComponent(location);
            if (category) url += '&category=' + category;
            
            // Update browser URL without reload
            window.history.pushState({}, '', url);
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                updateEventsList(data);
                updatePagination(data);
                updateResultsInfo(data);
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                loadingIndicator.classList.add('hidden');
                eventsContainer.style.opacity = '1';
                lucide.createIcons();
            });
        }
        
        function updateEventsList(data) {
            if (data.data && data.data.length > 0) {
                let html = '';
                data.data.forEach(event => {
                    const price = event.tickets && event.tickets.length > 0 
                        ? new Intl.NumberFormat('id-ID').format(Math.min(...event.tickets.map(t => t.price)))
                        : 0;
                    const priceText = price > 0 ? 'Rp ' + price : 'Habis Terjual';
                    const priceClass = price > 0 ? 'text-white' : 'text-sm text-gray-500';
                    
                    html += `
                        <div class="event-card">
                            <a href="/events/${event.id}" class="glass-card rounded-2xl overflow-hidden group hover:-translate-y-1 transition-all duration-300 block">
                                ${event.banner ? `
                                    <div class="aspect-video overflow-hidden">
                                        <img src="${event.banner.startsWith('http') ? event.banner : '/storage/' + event.banner}" 
                                             alt="${escapeHtml(event.title)}" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                    </div>
                                ` : `
                                    <div class="aspect-video bg-gradient-to-br from-[#6a5af9]/40 to-[#0b0b0f] flex items-center justify-center">
                                        <i data-lucide="calendar" class="w-12 h-12 text-white/20"></i>
                                    </div>
                                `}
                                <div class="p-4">
                                    <span class="text-xs font-bold text-[#6a5af9] uppercase">${escapeHtml(event.category?.name || 'Event')}</span>
                                    <h3 class="font-bold text-lg mb-2 mt-1 group-hover:text-[#6a5af9] transition-colors line-clamp-2">
                                        ${escapeHtml(event.title)}
                                    </h3>
                                    <div class="space-y-2 text-sm text-gray-400 mb-4">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="calendar" class="w-4 h-4"></i>
                                            ${new Date(event.start_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })} ${new Date(event.start_date).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} WIB
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                                            ${escapeHtml(event.location.length > 30 ? event.location.substring(0, 30) + '...' : event.location)}
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-lg font-bold ${priceClass}">
                                            ${priceText}
                                        </span>
                                        <div class="bg-white/10 group-hover:bg-[#6a5af9] p-2 rounded-xl transition-colors">
                                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    `;
                });
                eventsContainer.innerHTML = html;
            } else {
                eventsContainer.innerHTML = `
                    <div class="col-span-full text-center py-16 glass-card rounded-3xl">
                        <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-white/5 flex items-center justify-center">
                            <i data-lucide="calendar-x" class="w-12 h-12 text-gray-500"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">Tidak Ada Event</h3>
                        <p class="text-gray-400 mb-6">Tidak ditemukan event yang sesuai dengan filter Anda</p>
                        <button onclick="resetFilters()" class="inline-block bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] hover:opacity-90 text-white px-6 py-3 rounded-xl font-bold transition-all">
                            Reset Filter
                        </button>
                    </div>
                `;
            }
            lucide.createIcons();
        }
        
        function updatePagination(data) {
            if (data.links && data.links.length > 0) {
                let paginationHtml = `
                    <nav class="flex justify-center items-center gap-2">
                        ${data.prev_page_url ? `<a href="#" data-page="${data.current_page - 1}" class="pagination-link px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 transition-colors">Sebelumnya</a>` : `<span class="px-3 py-2 rounded-lg bg-white/5 text-gray-500 cursor-not-allowed">Sebelumnya</span>`}
                        <span class="px-4 py-2 rounded-lg bg-[#ff2d55] text-white">${data.current_page}</span>
                        ${data.next_page_url ? `<a href="#" data-page="${data.current_page + 1}" class="pagination-link px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 transition-colors">Selanjutnya</a>` : `<span class="px-3 py-2 rounded-lg bg-white/5 text-gray-500 cursor-not-allowed">Selanjutnya</span>`}
                    </nav>
                `;
                paginationContainer.innerHTML = paginationHtml;
                
                document.querySelectorAll('.pagination-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        currentPage = parseInt(this.dataset.page);
                        fetchEvents();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                });
            }
        }
        
        function updateResultsInfo(data) {
            const from = data.from || 0;
            const to = data.to || 0;
            const total = data.total || 0;
            showingStartSpan.textContent = from;
            showingEndSpan.textContent = to;
            totalResultsSpan.textContent = total;
            
            if (total > 0) {
                resultsInfo.classList.remove('hidden');
            } else {
                resultsInfo.classList.add('hidden');
            }
        }
        
        function resetFilters() {
            searchInput.value = '';
            locationInput.value = '';
            currentPage = 1;
            
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            url.searchParams.delete('location');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        
        // Event listeners
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                fetchEvents();
            }, 500);
        });
        
        locationInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                fetchEvents();
            }, 500);
        });
        
        resetFilterBtn.addEventListener('click', resetFilters);
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>