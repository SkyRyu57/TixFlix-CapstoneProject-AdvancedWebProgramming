<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') | Tixflix</title>
    
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
        
        /* SIDEBAR - FIXED: TAMPIL DI DESKTOP */
        .sidebar {
            transition: transform 0.3s ease-in-out;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 1030;
            width: 280px;
        }
        
        /* Desktop: sidebar tampil */
        @media (min-width: 1024px) {
            .sidebar {
                transform: translateX(0) !important;
            }
            .sidebar-overlay {
                display: none !important;
            }
        }
        
        /* Mobile: sidebar tersembunyi, muncul saat open */
        @media (max-width: 1023px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1020;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
        
        /* Modal Notifikasi & Profile */
        .modal-custom {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1050;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0.2s, opacity 0.2s;
        }
        .modal-custom.show {
            visibility: visible;
            opacity: 1;
        }
        .modal-content-custom {
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            border-radius: 1.5rem;
        }
        .modal-small {
            max-width: 300px;
        }
        .notification-item {
            word-break: break-word;
            white-space: normal;
        }
        .notification-message {
            word-break: break-word;
            white-space: normal;
            line-height: 1.4;
        }
        
        @media print {
            .sidebar, .no-print, .stat-card, .glass-panel, .glass-card, nav { display: none !important; }
            .flex.h-screen { display: block !important; }
            .flex-1 { margin-left: 0 !important; width: 100% !important; }
            main { padding: 0 !important; }
            table { border-collapse: collapse !important; width: 100% !important; }
            th, td { border: 1px solid #000 !important; padding: 8px !important; text-align: left !important; }
            th { background-color: #f0f0f0 !important; font-weight: bold !important; }
            .glass-card { background: white !important; border: 1px solid #ddd !important; box-shadow: none !important; }
            body, .text-gray-100, .text-gray-400, .text-gray-300 { color: black !important; }
            .text-gradient { color: black !important; background: none !important; -webkit-text-fill-color: black !important; }
            canvas { display: none !important; }
            .print-header { display: block !important; text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; }
            .print-footer { display: block !important; text-align: center; margin-top: 20px; border-top: 1px solid #ddd; font-size: 10px; }
        }
        .print-header, .print-footer { display: none; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#0b0b0f] text-gray-100">
    
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-[#ff2d55]/10 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] rounded-full bg-[#6a5af9]/10 blur-[120px]"></div>
    </div>
    
    <div class="relative">
        <!-- Overlay -->
        <div id="sidebarOverlay" class="sidebar-overlay"></div>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar w-72 glass-panel border-r border-white/10 flex flex-col h-full overflow-y-auto z-50">
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='{{ route('dashboard') }}'">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center">
                        <i data-lucide="ticket" class="w-6 h-6 text-white transform -rotate-12"></i>
                    </div>
                    <span class="text-xl font-bold">Tix<span class="text-gradient">flix</span></span>
                </div>
                <p class="text-xs text-gray-400 mt-2">Admin Panel</p>
            </div>
            
            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.events.index') }}" class="sidebar-item {{ request()->routeIs('admin.events.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    <span>Event</span>
                </a>
                <a href="{{ route('admin.transactions.index') }}" class="sidebar-item {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                    <span>Transaksi</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span>Pengguna</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="sidebar-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
                    <i data-lucide="tag" class="w-5 h-5"></i>
                    <span>Kategori</span>
                </a>
                <a href="{{ route('admin.payments.index') }}" class="sidebar-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
                    <i data-lucide="file-check" class="w-5 h-5"></i>
                    <span>Konfirmasi Pembayaran</span>
                </a>
            </nav>
            
            <!-- Notification Button -->
            <div class="px-4 py-2 border-b border-white/10">
                <button id="notificationBtn" class="relative p-2 rounded-lg hover:bg-white/5 transition w-full flex items-center gap-3">
                    <div class="relative">
                        <i data-lucide="bell" class="w-5 h-5 text-gray-400"></i>
                        <span id="notificationBadge" class="absolute -top-1 -right-1 w-4 h-4 bg-[#ff2d55] text-white text-[10px] rounded-full flex items-center justify-center hidden">0</span>
                    </div>
                    <span class="text-sm text-gray-300">Notifikasi</span>
                </button>
            </div>
            
            <!-- Profile Button (without dropdown) -->
            <div class="p-4 border-t border-white/10">
                <button id="profileBtn" class="w-full flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-colors">
                    <div class="w-10 h-10 rounded-full overflow-hidden bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}?t={{ time() }}" class="w-full h-full object-cover">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=f97316&color=fff&size=120" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1 text-left">
                        <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                    </div>
                    <i data-lucide="chevron-up" class="w-4 h-4 text-gray-400"></i>
                </button>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 lg:ml-72 min-h-screen">
            <nav class="glass-panel border-b border-white/10 px-6 py-3 flex items-center justify-between sticky top-0 z-20">
                <button id="sidebarToggle" class="lg:hidden text-white focus:outline-none">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <div class="flex items-center gap-4">
                    <div class="text-sm text-gray-300 hidden lg:block">Admin: {{ auth()->user()->name }}</div>
                </div>
            </nav>
            <div class="p-8">
                @if(session('success'))
                    <div class="mb-6 bg-green-500/10 border-l-4 border-green-500 text-green-500 p-4 rounded-r-lg no-print">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-6 bg-red-500/10 border-l-4 border-red-500 text-red-500 p-4 rounded-r-lg no-print">{{ session('error') }}</div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>
    
    <!-- Modal Notifikasi -->
    <div id="notificationModal" class="modal-custom">
        <div class="modal-content-custom glass-card rounded-2xl overflow-hidden">
            <div class="p-4 border-b border-white/10 flex justify-between items-center sticky top-0 bg-[#1e293b]">
                <h3 class="text-lg font-semibold">Notifikasi</h3>
                <button id="closeModalBtn" class="text-gray-400 hover:text-white transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div id="modalNotificationList" class="overflow-y-auto" style="max-height: calc(80vh - 70px);">
                <div class="p-4 text-center text-gray-500">Memuat...</div>
            </div>
        </div>
    </div>
    
    <!-- Modal Profile -->
    <div id="profileModal" class="modal-custom">
        <div class="modal-content-custom modal-small glass-card rounded-2xl overflow-hidden">
            <div class="p-4 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-lg font-semibold">Akun</h3>
                <button id="closeProfileModal" class="text-gray-400 hover:text-white transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="p-2">
                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 rounded-lg transition-colors">
                    <i data-lucide="user" class="w-5 h-5"></i>
                    <span>Profil Saya</span>
                </a>
                <div class="border-t border-white/10 my-1"></div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 rounded-lg transition-colors w-full text-left">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            
            // Sidebar toggle
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (toggleBtn && sidebar && overlay) {
                function closeSidebar() {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                }
                function openSidebar() {
                    sidebar.classList.add('open');
                    overlay.classList.add('show');
                }
                toggleBtn.addEventListener('click', () => {
                    if (sidebar.classList.contains('open')) closeSidebar();
                    else openSidebar();
                });
                overlay.addEventListener('click', closeSidebar);
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            }
            
            // Profile Modal
            const profileBtn = document.getElementById('profileBtn');
            const profileModal = document.getElementById('profileModal');
            const closeProfileModal = document.getElementById('closeProfileModal');
            
            if (profileBtn && profileModal && closeProfileModal) {
                profileBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    profileModal.classList.add('show');
                });
                closeProfileModal.addEventListener('click', () => {
                    profileModal.classList.remove('show');
                });
                profileModal.addEventListener('click', (e) => {
                    if (e.target === profileModal) profileModal.classList.remove('show');
                });
            }
            
            // ========================
            // NOTIFIKASI MODAL
            // ========================
            const notifBtn = document.getElementById('notificationBtn');
            const modal = document.getElementById('notificationModal');
            const modalList = document.getElementById('modalNotificationList');
            const closeModal = document.getElementById('closeModalBtn');
            const notifBadge = document.getElementById('notificationBadge');
            
            if (notifBtn && modal && modalList && closeModal && notifBadge) {
                function loadModalNotifications() {
                    fetch('{{ route("notifications.get") }}')
                        .then(response => response.json())
                        .then(data => {
                            updateNotifBadge(data.unread_count);
                            renderModalNotifications(data.notifications);
                        })
                        .catch(error => console.error('Error loading notifications:', error));
                }
                
                function updateNotifBadge(count) {
                    if (count > 0) {
                        notifBadge.textContent = count > 9 ? '9+' : count;
                        notifBadge.classList.remove('hidden');
                    } else {
                        notifBadge.classList.add('hidden');
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
                
                function escapeHtml(text) {
                    if (!text) return '';
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }
                
                function renderModalNotifications(notifications) {
                    if (!notifications || notifications.length === 0) {
                        modalList.innerHTML = `<div class="p-6 text-center text-gray-500"><i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2"></i><p class="text-sm">Tidak ada notifikasi</p></div>`;
                        lucide.createIcons();
                        return;
                    }
                    let html = '';
                    notifications.forEach(notif => {
                        const iconColor = notif.type === 'success' ? 'text-green-400' : (notif.type === 'warning' ? 'text-yellow-400' : 'text-blue-400');
                        const iconName = notif.type === 'success' ? 'check-circle' : (notif.type === 'warning' ? 'alert-triangle' : 'bell');
                        html += `
                            <div class="notification-item p-4 hover:bg-white/5 transition-colors border-b border-white/5 cursor-pointer ${!notif.is_read ? 'bg-[#ff2d55]/5' : ''}" data-id="${notif.id}" data-link="${notif.link || '#'}">
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0"><i data-lucide="${iconName}" class="w-4 h-4 ${iconColor}"></i></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold">${escapeHtml(notif.title)}</p>
                                        <p class="text-xs text-gray-400 mt-0.5 notification-message">${escapeHtml(notif.message)}</p>
                                        <p class="text-xs text-gray-500 mt-1">${formatDate(notif.created_at)}</p>
                                    </div>
                                    <button class="delete-notif text-gray-500 hover:text-red-400 transition ml-2" data-id="${notif.id}"><i data-lucide="x" class="w-3 h-3"></i></button>
                                </div>
                            </div>
                        `;
                    });
                    modalList.innerHTML = html;
                    lucide.createIcons();
                    
                    // Mark as read & delete
                    document.querySelectorAll('#modalNotificationList .notification-item').forEach(item => {
                        item.addEventListener('click', function(e) {
                            if (e.target.closest('.delete-notif')) return;
                            const id = this.dataset.id, link = this.dataset.link;
                            fetch(`/notifications/${id}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                                .then(() => { if (link && link !== '#') window.location.href = link; else loadModalNotifications(); });
                        });
                    });
                    document.querySelectorAll('#modalNotificationList .delete-notif').forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const id = this.dataset.id;
                            if (confirm('Hapus notifikasi ini?')) {
                                fetch(`/notifications/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                                    .then(() => loadModalNotifications());
                            }
                        });
                    });
                }
                
                notifBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    loadModalNotifications();
                    modal.classList.add('show');
                });
                
                closeModal.addEventListener('click', () => {
                    modal.classList.remove('show');
                });
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) modal.classList.remove('show');
                });
                
                // Load notifikasi pertama kali untuk badge
                setTimeout(() => loadModalNotifications(), 1000);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>