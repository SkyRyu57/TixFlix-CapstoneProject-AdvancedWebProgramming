<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile - Tixflix</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b0b10; color: #ffffff; }
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
        input, select, textarea {
            transition: all 0.3s ease;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #ff2d55;
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 45, 85, 0.2);
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
<body class="bg-[#0b0b0f] text-gray-100">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-[#ff2d55]/10 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] rounded-full bg-[#6a5af9]/10 blur-[120px]"></div>
    </div>

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 glass-panel border-b border-white/10 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3 cursor-pointer group" onclick="window.location.href='{{ route('dashboard') }}'">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center shadow-lg shadow-[#ff2d55]/30 group-hover:scale-105 transition-transform">
                        <i data-lucide="ticket" class="w-6 h-6 text-white transform -rotate-12"></i>
                    </div>
                    <span class="text-xl md:text-2xl font-bold tracking-tight">Tix<span class="text-gradient">flix</span></span>
                </div>

                <!-- NAVBAR MENU YANG SUDAH DIUPDATE -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition-colors">
                        Beranda
                    </a>
                    <a href="{{ route('events.list') }}" class="text-gray-400 hover:text-white transition-colors">
                        Event
                    </a>
                    <a href="{{ route('my-tickets') }}" class="text-gray-400 hover:text-white transition-colors">
                        Tiket Saya
                    </a>
                    <a href="{{ route('profile') }}" class="text-[#ff2d55] font-medium relative group">
                        Profil Saya
                        <span class="absolute -bottom-1.5 left-0 w-full h-0.5 bg-[#ff2d55] rounded-full"></span>
                    </a>
                </div>

                <div class="flex items-center gap-2 md:gap-4">
                    <!-- NOTIFIKASI DROPDOWN -->
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
                    
                    <!-- PROFIL DROPDOWN -->
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

    <div class="max-w-4xl mx-auto px-4 py-12">
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ url()->previous() }}" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <h1 class="text-3xl font-bold">Profil Saya</h1>
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

        <div class="glass-card rounded-2xl p-6 md:p-8">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="flex flex-col md:flex-row gap-8">
                    <!-- Avatar Section -->
                    <div class="text-center">
                        <div class="relative inline-block">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                                     class="w-32 h-32 rounded-full object-cover border-4 border-[#ff2d55]">
                            @else
                                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center text-4xl font-bold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                            <label for="avatar" class="absolute bottom-0 right-0 p-2 bg-[#ff2d55] rounded-full cursor-pointer hover:scale-110 transition">
                                <i data-lucide="camera" class="w-4 h-4"></i>
                                <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Klik camera untuk ganti foto</p>
                    </div>

                    <!-- Form Section -->
                    <div class="flex-1 space-y-5">
                        <div>
                            <label class="block text-sm font-medium mb-2">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" 
                                   class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" 
                                   class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">No. Telepon</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number', auth()->user()->phone_number) }}" 
                                   class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors">
                            @error('phone_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="border-t border-white/10 pt-4">
                            <h3 class="font-semibold mb-3">Ganti Password</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Password Baru</label>
                                    <input type="password" name="new_password" 
                                           class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors"
                                           placeholder="Kosongkan jika tidak ingin ganti">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Konfirmasi Password Baru</label>
                                    <input type="password" name="new_password_confirmation" 
                                           class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors"
                                           placeholder="Konfirmasi password baru">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] rounded-xl font-semibold hover:shadow-lg transition-all">
                                <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                .catch(error => console.error('Error memuat notifikasi:', error));
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
        
        // Preview avatar
        document.getElementById('avatar')?.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const avatarDiv = document.querySelector('.relative.inline-block > div');
                    if (avatarDiv) {
                        avatarDiv.innerHTML = `<img src="${event.target.result}" class="w-32 h-32 rounded-full object-cover border-4 border-[#ff2d55]">`;
                    }
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
</body>
</html>