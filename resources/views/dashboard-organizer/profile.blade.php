<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile - Tixflix Organizer</title>

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
        
        /* Sidebar styles */
        .sidebar-item {
            transition: all 0.3s ease;
        }
        .sidebar-item:hover {
            background: rgba(255, 45, 85, 0.1);
            transform: translateX(5px);
        }
        .sidebar-item.active {
            background: linear-gradient(90deg, rgba(255, 45, 85, 0.2) 0%, rgba(255, 94, 58, 0.1) 100%);
            border-left: 3px solid #ff2d55;
        }
        
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

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-[#ff2d55]/10 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] rounded-full bg-[#6a5af9]/10 blur-[120px]"></div>
    </div>

    <div class="flex h-screen">
        <!-- Sidebar (sama seperti organizer dashboard) -->
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
                
                <a href="{{ route('organizer.attendees') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span>Attendees</span>
                </a>
            </nav>
            
            <!-- Profile Section with Dropdown (sama seperti organizer dashboard) -->
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
                        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition-colors bg-[#ff2d55]/10">
                            <i data-lucide="user" class="w-4 h-4 text-[#ff2d55]"></i>
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
            <div class="max-w-4xl mx-auto px-4 py-12">
                <div class="flex items-center gap-3 mb-8">
                    <a href="{{ route('organizer.dashboard') }}" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-3xl font-bold">My Profile</h1>
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

                                <div>
                                    <label class="block text-sm font-medium mb-2">Bio / Deskripsi Singkat</label>
                                    <textarea name="bio" rows="3" 
                                              class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors"
                                              placeholder="Ceritakan tentang dirimu...">{{ old('bio', auth()->user()->bio) }}</textarea>
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