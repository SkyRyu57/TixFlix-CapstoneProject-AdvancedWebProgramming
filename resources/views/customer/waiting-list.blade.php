<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan Perlu Dibayar - Tixflix</title>
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
        .btn-print { transition: all 0.3s ease; }
        .btn-print:hover { transform: translateY(-2px); }
        .dropdown-menu { position: absolute; right: 0; top: 100%; margin-top: 8px; min-width: 200px; z-index: 100; }
        .pending-pulse { animation: pulse 2s ease-in-out infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
    </style>
</head>
<body class="bg-[#0b0b0f] text-gray-100 font-sans antialiased min-h-screen">

    <!-- Background Effects -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-[#ff2d55]/10 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] rounded-full bg-[#6a5af9]/10 blur-[120px]"></div>
    </div>

    <!-- Navbar (salin dari my-tickets) -->
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
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white font-medium transition-colors">Beranda</a>
                    <a href="{{ route('events.list') }}" class="text-gray-400 hover:text-white font-medium transition-colors">Event</a>
                    <a href="{{ route('my-tickets') }}" class="text-white font-medium relative group">
                        Tiket Saya
                        <span class="absolute -bottom-1.5 left-0 w-full h-0.5 bg-[#ff2d55] rounded-full"></span>
                    </a>
                </div>

                <div class="flex items-center gap-2 md:gap-4">
                    <div class="relative">
                        <button id="notificationBtn" class="p-2 text-gray-400 hover:text-white transition-colors relative group">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            <span id="notificationBadge" class="absolute -top-1 -right-1 w-5 h-5 bg-[#ff2d55] text-white text-xs rounded-full flex items-center justify-center hidden">0</span>
                        </button>
                        <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 glass-card rounded-xl overflow-hidden hidden z-50">
                            <div class="p-3 border-b border-white/10 flex justify-between items-center">
                                <h3 class="font-semibold">Notifikasi</h3>
                                <button id="markAllReadBtn" class="text-xs text-[#ff2d55] hover:text-white transition-colors">Tandai semua</button>
                            </div>
                            <div id="notificationList" class="max-h-96 overflow-y-auto">
                                <div class="p-4 text-center text-gray-500"><i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2"></i><p class="text-sm">Memuat notifikasi...</p></div>
                            </div>
                        </div>
                    </div>
                    
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
                        <div id="userDropdown" class="dropdown-menu glass-card rounded-xl overflow-hidden hidden">
                            <div class="p-3 border-b border-white/10"><p class="font-semibold text-sm">{{ auth()->user()->name }}</p><p class="text-xs text-gray-400">{{ auth()->user()->email }}</p></div>
                            <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition-colors"><i data-lucide="user" class="w-4 h-4"></i><span class="text-sm">My Profile</span></a>
                            <a href="{{ route('my-tickets') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition-colors"><i data-lucide="ticket" class="w-4 h-4"></i><span class="text-sm">My Tickets</span></a>
                            <div class="border-t border-white/10"></div>
                            <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition-colors w-full text-left"><i data-lucide="log-out" class="w-4 h-4"></i><span class="text-sm">Logout</span></button></form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-28 pb-20 px-6 max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">💳 Pesanan Perlu Dibayar</h1>
            <a href="{{ route('my-tickets') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl font-medium transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Tiket Saya
            </a>
        </div>

        @if(isset($waitingItems) && count($waitingItems) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($waitingItems as $item)
                    @php
                        $expiresAt = \Carbon\Carbon::parse($item->expires_at);
                        $isExpired = $expiresAt->isPast();
                    @endphp
                    <div class="glass-card rounded-2xl p-5 border-l-4 border-[#ff2d55] relative overflow-hidden">
                        <div class="absolute top-3 right-3">
                            @if($isExpired)
                                <span class="bg-red-500/20 text-red-400 text-xs px-2 py-1 rounded-full">Kadaluarsa</span>
                            @else
                                <span class="bg-green-500/20 text-green-400 text-xs px-2 py-1 rounded-full flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3 h-3"></i> {{ $expiresAt->diffInHours() }}j {{ $expiresAt->diffInMinutes() % 60 }}m
                                </span>
                            @endif
                        </div>
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold mb-1">{{ $item->event_title }}</h3>
                                <p class="text-sm text-gray-400 mb-2">{{ $item->ticket_name }} x{{ $item->quantity }}</p>
                                <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                                    <div><span class="text-gray-500">📅 Tanggal:</span> {{ \Carbon\Carbon::parse($item->event_start_date)->format('d M Y H:i') }} WIB</div>
                                    <div><span class="text-gray-500">📍 Lokasi:</span> {{ $item->event_location }}</div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-green-400">Rp {{ number_format($item->ticket_price * $item->quantity, 0, ',', '.') }}</span>
                                    @if(!$isExpired)
                                        <a href="{{ route('waiting.checkout', $item->id) }}" class="bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] text-white px-4 py-2 rounded-lg text-sm font-bold hover:opacity-90 transition">
                                            Bayar Sekarang
                                        </a>
                                    @else
                                        <span class="text-gray-500 text-sm italic">Kadaluarsa</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="glass-card rounded-2xl p-6 text-center">
                <i data-lucide="credit-card" class="w-12 h-12 mx-auto mb-3 text-gray-500"></i>
                <p class="text-gray-400">Tidak ada pesanan yang perlu dibayar.</p>
            </div>
        @endif
    </div>

    <script>
        lucide.createIcons();
        
        // Dropdown toggle
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userDropdown');
        if (userMenuBtn) { userMenuBtn.addEventListener('click', function(e) { e.stopPropagation(); userDropdown.classList.toggle('hidden'); }); }
        document.addEventListener('click', function() { if (userDropdown) userDropdown.classList.add('hidden'); });
        
        // Notifikasi (salin dari my-tickets)
        let notificationBtn = document.getElementById('notificationBtn');
        let notificationDropdown = document.getElementById('notificationDropdown');
        let notificationList = document.getElementById('notificationList');
        let notificationBadge = document.getElementById('notificationBadge');

        function loadNotifications() {
            fetch('{{ route("notifications.get") }}').then(r=>r.json()).then(d=>{ updateNotificationBadge(d.unread_count); renderNotifications(d.notifications); }).catch(e=>console.error(e));
        }
        function updateNotificationBadge(c) { if(c>0){ notificationBadge.textContent=c>9?'9+':c; notificationBadge.classList.remove('hidden'); } else { notificationBadge.classList.add('hidden'); } }
        function renderNotifications(notifs) {
            if(!notifs||notifs.length===0){ notificationList.innerHTML='<div class="p-6 text-center text-gray-500"><i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2"></i><p class="text-sm">Tidak ada notifikasi</p></div>'; lucide.createIcons(); return; }
            let html='';
            notifs.forEach(n=>{ const iconColor=n.type==='success'?'text-green-400':(n.type==='warning'?'text-yellow-400':'text-blue-400'); const iconName=n.type==='success'?'check-circle':(n.type==='warning'?'alert-triangle':'bell');
            html+=`<div class="notification-item p-3 hover:bg-white/5 transition-colors border-b border-white/5 cursor-pointer ${!n.is_read?'bg-[#ff2d55]/5':''}" data-id="${n.id}" data-link="${n.link||'#'}"><div class="flex gap-3"><div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0"><i data-lucide="${iconName}" class="w-4 h-4 ${iconColor}"></i></div><div class="flex-1 min-w-0"><p class="text-sm font-semibold">${escapeHtml(n.title)}</p><p class="text-xs text-gray-400 mt-0.5">${escapeHtml(n.message)}</p><p class="text-xs text-gray-500 mt-1">${formatDate(n.created_at)}</p></div><button class="delete-notif text-gray-500 hover:text-red-400 transition" data-id="${n.id}"><i data-lucide="x" class="w-3 h-3"></i></button></div></div>`; });
            notificationList.innerHTML=html; lucide.createIcons();
            document.querySelectorAll('.notification-item').forEach(i=>{ i.addEventListener('click',function(e){ if(e.target.closest('.delete-notif'))return; const id=this.dataset.id,link=this.dataset.link; fetch(`/notifications/${id}/read`,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>{ if(link&&link!=='#')window.location.href=link; else loadNotifications(); }); }); });
            document.querySelectorAll('.delete-notif').forEach(b=>{ b.addEventListener('click',function(e){ e.stopPropagation(); const id=this.dataset.id; if(confirm('Hapus notifikasi ini?')) fetch(`/notifications/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>loadNotifications()); }); });
        }
        function formatDate(dateString) { const date = new Date(dateString); const now = new Date(); const diff = Math.floor((now - date) / 1000); if (diff < 60) return 'Baru saja'; if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu'; if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu'; if (diff < 604800) return Math.floor(diff / 86400) + ' hari lalu'; return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }); }
        function escapeHtml(t){ if(!t)return ''; const d=document.createElement('div'); d.textContent=t; return d.innerHTML; }
        if(notificationBtn){ notificationBtn.addEventListener('click',function(e){ e.stopPropagation(); notificationDropdown.classList.toggle('hidden'); if(!notificationDropdown.classList.contains('hidden'))loadNotifications(); }); }
        document.getElementById('markAllReadBtn')?.addEventListener('click',function(){ fetch('{{ route("notifications.readAll") }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>loadNotifications()); });
        document.addEventListener('click',function(){ if(notificationDropdown)notificationDropdown.classList.add('hidden'); });
        setTimeout(()=>{ loadNotifications(); },1000);
    </script>
</body>
</html>