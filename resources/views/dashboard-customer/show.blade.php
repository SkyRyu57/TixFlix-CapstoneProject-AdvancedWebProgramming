<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->title }} - Tixflix</title>

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
        
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        
        .alert-slide {
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            animation: slideInRight 0.3s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-[#0b0b0f] text-gray-100 font-sans antialiased min-h-screen selection:bg-[#ff2d55] selection:text-white pb-20">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-[#ff2d55]/10 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] rounded-full bg-[#6a5af9]/10 blur-[120px]"></div>
    </div>

    <!-- NAVBAR -->
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
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white font-medium transition-colors">Home</a>
                    <a href="{{ route('concerts') }}" class="text-gray-400 hover:text-white font-medium transition-colors">Concerts</a>
                    <a href="{{ route('festivals') }}" class="text-gray-400 hover:text-white font-medium transition-colors">Festivals</a>
                    <a href="{{ route('my-tickets') }}" class="text-gray-400 hover:text-white font-medium transition-colors">My Tickets</a>
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
                                <div class="p-4 text-center text-gray-500">
                                    <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2"></i>
                                    <p class="text-sm">Memuat notifikasi...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative flex items-center gap-3 md:pl-4 md:border-l border-white/10">
                        <div class="hidden md:block text-right">
                            <div class="text-sm font-semibold">{{ auth()->user()->name ?? 'Guest User' }}</div>
                            <div class="text-xs text-gray-400">Event Explorer</div>
                        </div>
                        <div class="w-10 h-10 rounded-full border-2 border-[#1e1e28] bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center text-white font-bold hover:border-[#ff2d55] transition-colors cursor-pointer shadow-lg shadow-[#ff2d55]/20">
                            {{ substr(auth()->user()->name ?? 'G', 0, 1) }}
                        </div>
                        
                        <form action="{{ route('logout') }}" method="POST" class="ml-2">
                            @csrf
                            <button type="submit" class="p-2 bg-white/5 hover:bg-[#ff2d55]/10 text-gray-300 hover:text-[#ff2d55] rounded-xl transition-all duration-300" title="Logout">
                                <i data-lucide="log-out" class="w-5 h-5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                <span>Kembali</span>
            </a>
        </div>
        
        <!-- SESSION MESSAGES -->
        @if(session('success'))
            <div class="mb-6 bg-green-500/10 border-l-4 border-green-500 text-green-500 p-4 rounded-r-lg flex items-center gap-3 alert-slide">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <p class="font-bold">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-500/10 border-l-4 border-red-500 text-red-500 p-4 rounded-r-lg flex items-center gap-3 alert-slide">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <p class="font-bold">{{ session('error') }}</p>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 bg-blue-500/10 border-l-4 border-blue-500 text-blue-400 p-4 rounded-r-lg flex items-center gap-3 alert-slide">
                <i data-lucide="info" class="w-5 h-5"></i>
                <p class="font-bold">{{ session('info') }}</p>
            </div>
        @endif
        
        <!-- Event Header -->
        <div class="glass-card rounded-3xl overflow-hidden mb-8">
            @if($event->banner)
                <div class="relative h-64 md:h-96">
                    <img src="{{ Str::startsWith($event->banner, 'http') ? $event->banner : asset('storage/' . $event->banner) }}" 
                         alt="{{ $event->title }}" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0b0b0f] via-transparent to-transparent"></div>
                </div>
            @endif
            
            <div class="p-6 md:p-8">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#ff2d55]/20 text-[#ff2d55]">
                        {{ $event->category->name ?? 'Event' }}
                    </span>
                    <div class="flex items-center gap-2 text-sm text-gray-400">
                        <i data-lucide="star" class="w-4 h-4 text-yellow-400 fill-yellow-400"></i>
                        <span>{{ number_format($event->avg_rating ?? 0, 1) }} ({{ $event->total_reviews ?? 0 }} reviews)</span>
                    </div>
                </div>
                
                <h1 class="text-3xl md:text-4xl font-bold mb-4">{{ $event->title }}</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-300">
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar" class="w-5 h-5 text-[#ff2d55]"></i>
                        <span>{{ \Carbon\Carbon::parse($event->start_date)->format('l, d F Y') }}</span>
                        <span class="text-gray-500">|</span>
                        <span>{{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }} WIB</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-5 h-5 text-[#ff2d55]"></i>
                        <span>{{ $event->location }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-5 h-5 text-[#ff2d55]"></i>
                        <span>Doors open: 1 jam sebelum acara</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Event Description -->
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-card rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <i data-lucide="info" class="w-5 h-5 text-[#ff2d55]"></i>
                        Tentang Event
                    </h2>
                    <div class="prose prose-invert max-w-none">
                        <p class="text-gray-300 leading-relaxed">{{ $event->description }}</p>
                    </div>
                </div>
                
                @if(isset($relatedEvents) && $relatedEvents->count() > 0)
                <div class="glass-card rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <i data-lucide="calendar" class="w-5 h-5 text-[#ff2d55]"></i>
                        Event Terkait
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($relatedEvents as $related)
                        <a href="{{ url('/events/' . $related->id) }}" class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-colors">
                            @if($related->banner)
                                <img src="{{ asset('storage/' . $related->banner) }}" class="w-12 h-12 rounded-lg object-cover">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#ff2d55]/20 to-[#ff5e3a]/20 flex items-center justify-center">
                                    <i data-lucide="calendar" class="w-6 h-6 text-gray-500"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="font-semibold text-sm">{{ $related->title }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($related->start_date)->format('d M Y') }}</p>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-500"></i>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Ticket Selection -->
            <div class="lg:col-span-1">
                <div class="glass-card rounded-2xl p-6 sticky top-24">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <i data-lucide="ticket" class="w-5 h-5 text-[#ff2d55]"></i>
                        Pilih Tiket
                    </h2>
                    
                    <form action="{{ route('checkout.process') }}" method="POST" id="ticketForm">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                        
                        <div class="space-y-4 mb-6">
                            @forelse($event->tickets as $ticket)
                                @php
                                    $availableStock = $ticket->stock - \App\Models\Eticket::where('ticket_id', $ticket->id)->count();
                                    $isWaiting = \App\Models\WaitingList::where('user_id', auth()->id())
                                        ->where('ticket_id', $ticket->id)
                                        ->where('status', 'waiting')
                                        ->exists();
                                    $isNotified = \App\Models\WaitingList::where('user_id', auth()->id())
                                        ->where('ticket_id', $ticket->id)
                                        ->where('status', 'notified')
                                        ->exists();
                                @endphp
                                
                                <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="font-semibold">{{ $ticket->name }}</h3>
                                            @if($ticket->description)
                                                <p class="text-xs text-gray-500 mt-1">{{ $ticket->description }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-green-400">Rp {{ number_format($ticket->price, 0, ',', '.') }}</p>
                                            <p class="text-xs text-gray-500">Stok: {{ $availableStock }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($availableStock > 0)
                                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-white/10">
                                            <span class="text-sm text-gray-400">Jumlah</span>
                                            <div class="flex items-center gap-2">
                                                <button type="button" class="qty-minus w-8 h-8 rounded-lg bg-white/10 hover:bg-[#ff2d55]/20 text-white transition-colors" data-ticket-id="{{ $ticket->id }}">
                                                    <i data-lucide="minus" class="w-4 h-4 mx-auto"></i>
                                                </button>
                                                <input type="number" name="tickets[{{ $ticket->id }}]" 
                                                       class="qty-input w-16 text-center bg-transparent border border-white/10 rounded-lg py-1"
                                                       value="0" min="0" max="{{ $availableStock }}" step="1">
                                                <button type="button" class="qty-plus w-8 h-8 rounded-lg bg-white/10 hover:bg-[#ff2d55]/20 text-white transition-colors" data-ticket-id="{{ $ticket->id }}">
                                                    <i data-lucide="plus" class="w-4 h-4 mx-auto"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mt-3 pt-3 border-t border-white/10">
                                            @if(!$isWaiting && !$isNotified)
                                                <button type="button" 
                                                        onclick="joinWaitingList({{ $ticket->id }})"
                                                        class="w-full py-2 bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-400 rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2">
                                                    <i data-lucide="bell" class="w-4 h-4"></i>
                                                    Daftar Waiting List
                                                </button>
                                                <p class="text-xs text-gray-500 text-center mt-2">
                                                    Tiket habis? Daftar waiting list, kami akan memberi tahu jika tersedia.
                                                </p>
                                            @elseif($isWaiting)
                                                <div class="text-center">
                                                    <div class="py-2 bg-yellow-500/10 text-yellow-400 rounded-lg text-sm flex items-center justify-center gap-2">
                                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                        Anda sudah di waiting list
                                                    </div>
                                                    <button type="button" 
                                                            onclick="cancelWaitingList({{ $ticket->id }})"
                                                            class="text-xs text-gray-500 hover:text-red-400 transition-colors mt-2">
                                                        Batalkan waiting list
                                                    </button>
                                                </div>
                                            @elseif($isNotified)
                                                <div class="text-center">
                                                    <div class="py-2 bg-green-500/10 text-green-400 rounded-lg text-sm flex items-center justify-center gap-2">
                                                        <i data-lucide="bell" class="w-4 h-4"></i>
                                                        Tiket sudah tersedia!
                                                    </div>
                                                    <a href="{{ url('/events/' . $event->id) }}" class="text-xs text-green-400 hover:text-green-300 transition-colors mt-2 inline-block">
                                                        Beli tiket sekarang →
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <i data-lucide="ticket-x" class="w-12 h-12 text-gray-500 mx-auto mb-3"></i>
                                    <p class="text-gray-400">Belum ada tiket tersedia</p>
                                </div>
                            @endforelse
                        </div>
                        
                        @php
                            $hasAvailableTicket = $event->tickets->contains(function($ticket) {
                                return ($ticket->stock - \App\Models\Eticket::where('ticket_id', $ticket->id)->count()) > 0;
                            });
                        @endphp
                        
                        @if($hasAvailableTicket)
                            <div class="border-t border-white/10 pt-4 mb-4">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span id="totalPrice" class="text-green-400">Rp 0</span>
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] hover:opacity-90 text-white py-3 rounded-xl font-bold transition-all">
                                Beli Tiket
                            </button>
                        @else
                            <div class="text-center py-4">
                                <div class="bg-yellow-500/10 rounded-xl p-4">
                                    <i data-lucide="alert-circle" class="w-8 h-8 text-yellow-400 mx-auto mb-2"></i>
                                    <p class="text-yellow-400 font-semibold">Semua Tiket Habis!</p>
                                    <p class="text-xs text-gray-400 mt-1">Silakan daftar waiting list untuk tiket yang Anda inginkan di atas.</p>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        
        <!-- ======================================== -->
        <!-- REVIEW & RATING SECTION -->
        <!-- ======================================== -->
        @php
            $hasPurchased = \App\Models\Eticket::whereHas('ticket', function($q) use ($event) {
                $q->where('event_id', $event->id);
            })->where('user_id', auth()->id())->exists();
            
            $hasReviewed = \App\Models\Review::where('user_id', auth()->id())
                ->where('event_id', $event->id)
                ->exists();
            
            $isEventEnded = \Carbon\Carbon::parse($event->end_date)->isPast();
        @endphp

        <div class="mt-12">
            <!-- Rating Summary -->
            <div class="glass-card rounded-2xl p-6 mb-8">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                    Rating & Ulasan
                </h2>
                
                <div class="flex flex-col md:flex-row items-center gap-6 p-4 rounded-xl bg-white/5">
                    <div class="text-center">
                        <div class="text-5xl font-bold text-yellow-400">{{ number_format($event->avg_rating ?? 0, 1) }}</div>
                        <div class="flex gap-1 my-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i data-lucide="star" class="w-5 h-5 {{ $i <= round($event->avg_rating ?? 0) ? 'text-yellow-400 fill-yellow-400' : 'text-gray-500' }}"></i>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-400">{{ $event->total_reviews ?? 0 }} ulasan</p>
                    </div>
                    
                    <div class="flex-1 space-y-2">
                        @for($i = 5; $i >= 1; $i--)
                            @php
                                $count = \App\Models\Review::where('event_id', $event->id)->where('rating', $i)->count();
                                $percentage = ($event->total_reviews ?? 0) > 0 ? ($count / $event->total_reviews) * 100 : 0;
                            @endphp
                            <div class="flex items-center gap-3">
                                <span class="text-sm w-8">{{ $i }} ★</span>
                                <div class="flex-1 h-2 bg-white/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-yellow-400 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-xs text-gray-400 w-12">{{ $count }}</span>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
            
            <!-- Form Review -->
            @if($isEventEnded && $hasPurchased && !$hasReviewed)
            <div class="glass-card rounded-2xl p-6 mb-8">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-5 h-5 text-[#ff2d55]"></i>
                    Beri Rating & Ulasan
                </h2>
                
                <form action="{{ route('review.store', $event->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Rating Anda</label>
                        <div class="flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="rating-star text-3xl text-gray-400 hover:text-yellow-400 transition-all duration-200" data-rating="{{ $i }}">
                                    <i data-lucide="star" class="w-8 h-8"></i>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="ratingValue" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Ulasan (Opsional)</label>
                        <textarea name="comment" rows="4" 
                                class="w-full px-4 py-3 bg-[#0b0b0f] border border-white/10 rounded-xl focus:border-[#ff2d55] transition-colors"
                                placeholder="Bagikan pengalaman Anda menonton event ini..."></textarea>
                    </div>
                    
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-[#ff2d55] to-[#ff5e3a] rounded-xl font-semibold hover:shadow-lg transition-all">
                        <i data-lucide="send" class="w-4 h-4 inline mr-2"></i>
                        Kirim Ulasan
                    </button>
                </form>
            </div>
            @endif
            
            <!-- Reviews List -->
            @if(($event->total_reviews ?? 0) > 0)
            <div class="glass-card rounded-2xl p-6">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <i data-lucide="message-circle" class="w-5 h-5 text-[#ff2d55]"></i>
                    Ulasan Pengunjung ({{ $event->total_reviews ?? 0 }})
                </h2>
                
                <div id="reviewsList" class="space-y-4">
                    @foreach($event->reviews()->with('user')->latest()->take(5)->get() as $review)
                    <div class="p-4 rounded-xl bg-white/5 hover:bg-white/10 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center font-bold">
                                    {{ substr($review->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold">{{ $review->user->name }}</p>
                                    <div class="flex gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i data-lucide="star" class="w-3 h-3 {{ $i <= $review->rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-500' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                        <p class="text-sm text-gray-300 ml-13">{{ $review->comment ?? 'Tidak ada ulasan tertulis.' }}</p>
                    </div>
                    @endforeach
                </div>
                
                @if(($event->total_reviews ?? 0) > 5)
                <div class="text-center mt-4">
                    <button id="loadMoreReviews" class="text-sm text-[#ff2d55] hover:text-white transition-colors">
                        Lihat semua ulasan →
                    </button>
                </div>
                @endif
            </div>
            @endif
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <script>
        lucide.createIcons();
        
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
                notificationList.innerHTML = `<div class="p-6 text-center text-gray-500"><i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2"></i><p class="text-sm">Tidak ada notifikasi</p></div>`;
                lucide.createIcons();
                return;
            }
            
            let html = '';
            notifications.forEach(notif => {
                const iconColor = notif.type === 'success' ? 'text-green-400' : (notif.type === 'warning' ? 'text-yellow-400' : (notif.type === 'error' ? 'text-red-400' : 'text-blue-400'));
                const iconName = notif.type === 'success' ? 'check-circle' : (notif.type === 'warning' ? 'alert-triangle' : (notif.type === 'error' ? 'alert-circle' : 'bell'));
                
                html += `<div class="notification-item p-3 hover:bg-white/5 transition-colors border-b border-white/5 cursor-pointer ${!notif.is_read ? 'bg-[#ff2d55]/5' : ''}" data-id="${notif.id}" data-link="${notif.link || '#'}">
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="${iconName}" class="w-4 h-4 ${iconColor}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold">${escapeHtml(notif.title)}</p>
                                    <p class="text-xs text-gray-400 mt-1">${escapeHtml(notif.message)}</p>
                                    <p class="text-xs text-gray-500 mt-1">${formatDate(notif.created_at)}</p>
                                </div>
                                <button class="delete-notif text-gray-500 hover:text-red-400 transition" data-id="${notif.id}"><i data-lucide="x" class="w-3 h-3"></i></button>
                            </div>
                        </div>`;
            });
            
            notificationList.innerHTML = html;
            lucide.createIcons();
            
            document.querySelectorAll('.notification-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    if (e.target.closest('.delete-notif')) return;
                    const id = this.dataset.id;
                    const link = this.dataset.link;
                    fetch(`/notifications/${id}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => {
                        if (link && link !== '#') window.location.href = link;
                        else loadNotifications();
                    });
                });
            });
            
            document.querySelectorAll('.delete-notif').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const id = this.dataset.id;
                    if (confirm('Hapus notifikasi ini?')) {
                        fetch(`/notifications/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => loadNotifications());
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
                if (!notificationDropdown.classList.contains('hidden')) loadNotifications();
            });
        }

        document.getElementById('markAllReadBtn')?.addEventListener('click', function() {
            fetch('{{ route("notifications.readAll") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => loadNotifications());
        });

        document.addEventListener('click', function() {
            if (notificationDropdown) notificationDropdown.classList.add('hidden');
        });

        setTimeout(() => loadNotifications(), 1000);
        
        // ========================================
        // QUANTITY & TOTAL PRICE
        // ========================================
        function updateTotal() {
            let total = 0;
            document.querySelectorAll('.qty-input').forEach(input => {
                const ticketId = input.getAttribute('name').match(/\d+/)[0];
                const priceElement = input.closest('.p-4').querySelector('.text-lg.font-bold');
                if (priceElement) {
                    const price = parseInt(priceElement.textContent.replace(/[^0-9]/g, ''));
                    const qty = parseInt(input.value) || 0;
                    total += price * qty;
                }
            });
            document.getElementById('totalPrice').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }
        
        document.querySelectorAll('.qty-plus').forEach(btn => {
            btn.addEventListener('click', function() {
                const ticketId = this.dataset.ticketId;
                const input = document.querySelector(`input[name="tickets[${ticketId}]"]`);
                const max = parseInt(input.getAttribute('max'));
                let value = parseInt(input.value) || 0;
                if (value < max) {
                    input.value = value + 1;
                    updateTotal();
                }
            });
        });
        
        document.querySelectorAll('.qty-minus').forEach(btn => {
            btn.addEventListener('click', function() {
                const ticketId = this.dataset.ticketId;
                const input = document.querySelector(`input[name="tickets[${ticketId}]"]`);
                let value = parseInt(input.value) || 0;
                if (value > 0) {
                    input.value = value - 1;
                    updateTotal();
                }
            });
        });
        
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', function() {
                const max = parseInt(this.getAttribute('max'));
                let value = parseInt(this.value) || 0;
                if (value > max) this.value = max;
                if (value < 0) this.value = 0;
                updateTotal();
            });
        });
        
        updateTotal();
        
        // ========================================
        // WAITING LIST FUNCTIONS (AJAX)
        // ========================================
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast-notification glass-card rounded-xl p-4 flex items-center gap-3 ${type === 'success' ? 'border-green-500/30' : 'border-red-500/30'}`;
            toast.innerHTML = `
                <i data-lucide="${type === 'success' ? 'check-circle' : 'alert-circle'}" class="w-5 h-5 ${type === 'success' ? 'text-green-400' : 'text-red-400'}"></i>
                <p class="text-sm">${message}</p>
            `;
            document.body.appendChild(toast);
            lucide.createIcons();
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        function joinWaitingList(ticketId) {
            fetch('{{ url("/waitinglist") }}/' + ticketId + '/join', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: 1 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Berhasil masuk waiting list!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Gagal mendaftar waiting list', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
            });
        }
        
        function cancelWaitingList(ticketId) {
            if (confirm('Batalkan waiting list untuk tiket ini?')) {
                fetch('{{ url("/waitinglist") }}/' + ticketId + '/cancel', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Berhasil membatalkan waiting list', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Gagal membatalkan waiting list', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                });
            }
        }
        
        // ========================================
        // RATING STARS
        // ========================================
        let selectedRating = 0;
        document.querySelectorAll('.rating-star').forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = this.dataset.rating;
                document.getElementById('ratingValue').value = selectedRating;
                document.querySelectorAll('.rating-star').forEach((s, i) => {
                    const icon = s.querySelector('i');
                    if (i < selectedRating) {
                        icon.classList.add('text-yellow-400', 'fill-yellow-400');
                        icon.classList.remove('text-gray-400');
                    } else {
                        icon.classList.remove('text-yellow-400', 'fill-yellow-400');
                        icon.classList.add('text-gray-400');
                    }
                });
            });
        });
        
        // ========================================
        // LOAD MORE REVIEWS
        // ========================================
        document.getElementById('loadMoreReviews')?.addEventListener('click', function() {
            fetch('{{ route("reviews.get", $event->id) }}')
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    data.data.forEach(review => {
                        html += `<div class="p-4 rounded-xl bg-white/5 hover:bg-white/10 transition-colors">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center font-bold">${review.user.name.charAt(0)}</div>
                                            <div>
                                                <p class="font-semibold">${escapeHtml(review.user.name)}</p>
                                                <div class="flex gap-0.5">${Array(5).fill().map((_, i) => `<i data-lucide="star" class="w-3 h-3 ${i < review.rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-500'}"></i>`).join('')}</div>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500">${new Date(review.created_at).toLocaleDateString('id-ID')}</p>
                                    </div>
                                    <p class="text-sm text-gray-300 ml-13">${escapeHtml(review.comment || 'Tidak ada ulasan tertulis.')}</p>
                                </div>`;
                    });
                    document.getElementById('reviewsList').innerHTML = html;
                    lucide.createIcons();
                    this.style.display = 'none';
                });
        });
        
        // Auto hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert-slide').forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>