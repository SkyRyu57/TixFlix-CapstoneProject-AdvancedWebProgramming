<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Organizer Dashboard</title>
    
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
                <div class="flex items-center gap-3 mb-6">
                    <a href="{{ route('organizer.events') }}" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold">Edit Event</h1>
                        <p class="text-gray-400 mt-1">Update your event details</p>
                    </div>
                </div>
                
                <form action="{{ route('organizer.event.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="glass-card rounded-2xl p-6">
                        <h2 class="text-xl font-bold mb-6">Basic Information</h2>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Event Title -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium mb-2">Event Title *</label>
                                <input type="text" name="title" required value="{{ old('title', $event->title) }}"
                                       class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors">
                            </div>
                            
                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Category *</label>
                                <select name="category_id" required class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $event->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Status *</label>
                                <select name="status" required class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors">
                                    <option value="draft" {{ $event->status == 'draft' ? 'selected' : '' }}>Draft (Not visible to public)</option>
                                    <option value="published" {{ $event->status == 'published' ? 'selected' : '' }}>Published (Visible to public)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glass-card rounded-2xl p-6">
                        <h2 class="text-xl font-bold mb-6">Event Details</h2>
                        
                        <div class="space-y-6">
                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Description *</label>
                                <textarea name="description" rows="6" required 
                                          class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors">{{ old('description', $event->description) }}</textarea>
                            </div>
                            
                            <!-- Location -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Location *</label>
                                <input type="text" name="location" required value="{{ old('location', $event->location) }}"
                                       class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Start Date -->
                                <div>
                                    <label class="block text-sm font-medium mb-2">Start Date & Time *</label>
                                    <input type="datetime-local" name="start_date" required 
                                           value="{{ \Carbon\Carbon::parse($event->start_date)->format('Y-m-d\TH:i') }}"
                                           class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors">
                                </div>
                                
                                <!-- End Date -->
                                <div>
                                    <label class="block text-sm font-medium mb-2">End Date & Time *</label>
                                    <input type="datetime-local" name="end_date" required 
                                           value="{{ \Carbon\Carbon::parse($event->end_date)->format('Y-m-d\TH:i') }}"
                                           class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors">
                                </div>
                            </div>
                            
                            <!-- Banner Image -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Event Banner</label>
                                
                                @if($event->banner)
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-400 mb-2">Current Banner:</p>
                                        <img src="{{ asset('storage/' . $event->banner) }}" class="max-h-48 rounded-lg" alt="Current banner">
                                    </div>
                                @endif
                                
                                <div class="border-2 border-dashed border-white/20 rounded-xl p-6 text-center hover:border-[#ff2d55] transition-colors cursor-pointer" id="bannerUpload">
                                    <input type="file" name="banner" accept="image/*" class="hidden" id="bannerInput">
                                    <i data-lucide="upload" class="w-10 h-10 text-gray-400 mx-auto mb-2"></i>
                                    <p class="text-gray-400 text-sm">Click to upload new banner (optional)</p>
                                    <p class="text-gray-500 text-xs mt-1">PNG, JPG, GIF up to 2MB</p>
                                    <div id="bannerPreview" class="mt-4 hidden">
                                        <img id="previewImage" class="max-h-48 mx-auto rounded-lg" alt="Preview">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('organizer.events') }}" class="px-6 py-3 bg-white/10 hover:bg-white/20 rounded-xl font-semibold transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] rounded-xl font-semibold hover:shadow-lg transition-all">
                            <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                            Update Event
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
        lucide.createIcons();
        
        const bannerUpload = document.getElementById('bannerUpload');
        const bannerInput = document.getElementById('bannerInput');
        const bannerPreview = document.getElementById('bannerPreview');
        const previewImage = document.getElementById('previewImage');
        
        bannerUpload.addEventListener('click', () => {
            bannerInput.click();
        });
        
        bannerInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    previewImage.src = event.target.result;
                    bannerPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
        
        bannerUpload.addEventListener('dragover', (e) => {
            e.preventDefault();
            bannerUpload.classList.add('border-[#ff2d55]', 'bg-[#ff2d55]/5');
        });
        
        bannerUpload.addEventListener('dragleave', () => {
            bannerUpload.classList.remove('border-[#ff2d55]', 'bg-[#ff2d55]/5');
        });
        
        bannerUpload.addEventListener('drop', (e) => {
            e.preventDefault();
            bannerUpload.classList.remove('border-[#ff2d55]', 'bg-[#ff2d55]/5');
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                bannerInput.files = e.dataTransfer.files;
                const reader = new FileReader();
                reader.onload = (event) => {
                    previewImage.src = event.target.result;
                    bannerPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>