<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

// ============================================
// 1. HALAMAN UTAMA
// ============================================
Route::get('/', function () {
    return view('welcome');
});

// ============================================
// 2. AUTHENTICATION (GUEST)
// ============================================
Route::middleware('guest')->group(function () {
    Route::get('/register', function () { 
        return view('auth.register'); 
    })->name('register');
    
    Route::get('/login', function () { 
        return view('auth.login'); 
    })->name('login');
    
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
    
    // ========================================
    // FORGOT PASSWORD (LUPA PASSWORD) - DENGAN EMAILJS
    // ========================================
    
    // Form lupa password
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');
    
    // API: Cek email di database
    Route::post('/check-email', function (Request $request) {
        $email = $request->email;
        $user = \App\Models\User::where('email', $email)->first();
        
        if ($user) {
            return response()->json([
                'exists' => true,
                'user_name' => $user->name,
                'user_email' => $user->email
            ]);
        }
        
        return response()->json(['exists' => false]);
    })->name('check.email');
    
    // API: Buat token reset password
    Route::post('/create-reset-token', function (Request $request) {
        $email = $request->email;
        $user = \App\Models\User::where('email', $email)->first();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email tidak ditemukan']);
        }
        
        // Generate token
        $token = Str::random(64);
        
        // Simpan token ke database
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => $token, 'created_at' => now()]
        );
        
        // Buat link reset password
        $resetLink = url('/reset-password/' . $token . '?email=' . urlencode($email));
        
        return response()->json([
            'success' => true,
            'reset_link' => $resetLink,
            'token' => $token
        ]);
    })->name('create.reset.token');
    
    // Form reset password (GET)
    Route::get('/reset-password/{token}', function ($token) {
        $resetRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();
        
        if (!$resetRecord) {
            return redirect()->route('password.request')->with('error', 'Token tidak valid atau sudah kadaluarsa!');
        }
        
        // Cek apakah token sudah lebih dari 60 menit
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('token', $token)->delete();
            return redirect()->route('password.request')->with('error', 'Link reset password sudah kadaluarsa! Silakan coba lagi.');
        }
        
        return view('auth.reset-password', ['token' => $token, 'email' => $resetRecord->email]);
    })->name('password.reset');
    
    // Proses reset password (POST)
    Route::post('/reset-password', function (Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);
        
        $resetRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();
        
        if (!$resetRecord) {
            return back()->with('error', 'Token tidak valid!');
        }
        
        // Update password user
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $user->save();
        }
        
        // Hapus token
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();
        
        return redirect()->route('login')->with('success', 'Password berhasil direset! Silakan login dengan password baru Anda.');
    })->name('password.update');
});

// ============================================
// 3. TERPROTEKSI (Harus Login)
// ============================================
Route::middleware('auth')->group(function () {
    
    Route::get('/home', function () {
        $role = auth()->user()->role;
        if ($role == 'admin') return redirect()->route('admin.dashboard');
        if ($role == 'organizer') return redirect()->route('organizer.dashboard');
        return redirect()->route('dashboard');
    })->name('home');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/logout', function () {
        return redirect()->route('home');
    });

    // ========================================
    // PROFILE MANAGEMENT
    // ========================================
    Route::get('/profile', function () {
        if (auth()->user()->role == 'organizer') {
            return view('dashboard-organizer.profile');
        }
        return view('dashboard-customer.profile');
    })->name('profile');

    Route::put('/profile', function (Request $request) {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'new_password' => 'nullable|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->bio = $request->bio;
        
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }
        
        $user->save();
        
        return back()->with('success', 'Profile berhasil diperbarui!');
    })->name('profile.update');

    // ========================================
    // NOTIFICATIONS
    // ========================================
    Route::get('/notifications', function () {
        $notifications = \App\Models\Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        foreach ($notifications as $notif) {
            $notif->time_ago = $notif->created_at->diffForHumans();
        }
        
        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    })->name('notifications.get');
    
    Route::post('/notifications/{id}/read', function ($id) {
        $notification = \App\Models\Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->update(['is_read' => true]);
        return response()->json(['success' => true]);
    })->name('notifications.read');
    
    Route::post('/notifications/read-all', function () {
        \App\Models\Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return response()->json(['success' => true]);
    })->name('notifications.readAll');
    
    Route::delete('/notifications/{id}', function ($id) {
        $notification = \App\Models\Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->delete();
        return response()->json(['success' => true]);
    })->name('notifications.delete');

    // ========================================
    // ADMIN DASHBOARD
    // ========================================
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('dashboard-admin.admin');
        })->name('admin.dashboard');
    });

    // ========================================
    // ORGANIZER DASHBOARD
    // ========================================
    Route::middleware('role:organizer')->group(function () {
        
        Route::get('/organizer/dashboard', function () {
            $events = \App\Models\Event::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            $totalEvents = \App\Models\Event::where('user_id', auth()->id())->count();
            $totalTicketsSold = \App\Models\Eticket::whereHas('ticket.event', function($q) {
                $q->where('user_id', auth()->id());
            })->count();
            
            $totalRevenue = \App\Models\Eticket::whereHas('ticket.event', function($q) {
                $q->where('user_id', auth()->id());
            })->join('tickets', 'etickets.ticket_id', '=', 'tickets.id')
              ->sum('tickets.price');
            
            $upcomingEvents = \App\Models\Event::where('user_id', auth()->id())
                ->where('status', 'published')
                ->where('start_date', '>=', now())
                ->count();
            
            $allEvents = \App\Models\Event::with('category', 'tickets')
                ->where('user_id', auth()->id())
                ->get();
            
            $eventFinancials = [];
            $eventLabels = [];
            $eventSalesData = [];
            $eventRevenueData = [];
            
            foreach ($allEvents as $event) {
                $ticketsSold = \App\Models\Eticket::whereHas('ticket', function($q) use ($event) {
                    $q->where('event_id', $event->id);
                })->count();
                
                $revenue = \App\Models\Eticket::whereHas('ticket', function($q) use ($event) {
                    $q->where('event_id', $event->id);
                })->join('tickets', 'etickets.ticket_id', '=', 'tickets.id')
                  ->sum('tickets.price');
                
                $ticketDetails = [];
                foreach ($event->tickets as $ticket) {
                    $sold = \App\Models\Eticket::where('ticket_id', $ticket->id)->count();
                    $ticketDetails[] = [
                        'name' => $ticket->name,
                        'price' => $ticket->price,
                        'stock' => $ticket->stock,
                        'sold' => $sold,
                        'revenue' => $sold * $ticket->price,
                    ];
                }
                
                $eventFinancials[] = [
                    'id' => $event->id,
                    'title' => $event->title,
                    'category' => $event->category->name ?? 'Uncategorized',
                    'banner' => $event->banner,
                    'start_date' => $event->start_date,
                    'location' => $event->location,
                    'tickets_sold' => $ticketsSold,
                    'revenue' => $revenue,
                    'platform_fee' => $revenue * 0.1,
                    'net_income' => $revenue * 0.9,
                    'ticket_details' => $ticketDetails,
                ];
                
                $eventLabels[] = Str::limit($event->title, 15);
                $eventSalesData[] = $ticketsSold;
                $eventRevenueData[] = $revenue;
            }
            
            return view('dashboard-organizer.organizer', compact(
                'events', 'totalEvents', 'totalTicketsSold', 'totalRevenue', 'upcomingEvents',
                'eventFinancials', 'eventLabels', 'eventSalesData', 'eventRevenueData'
            ));
        })->name('organizer.dashboard');
        
        Route::get('/organizer/events', function () {
            $events = \App\Models\Event::with(['category'])
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            return view('dashboard-organizer.events', compact('events'));
        })->name('organizer.events');
        
        Route::get('/organizer/events/create', function () {
            $categories = \App\Models\Category::all();
            return view('dashboard-organizer.event-create', compact('categories'));
        })->name('organizer.event.create');
        
        Route::post('/organizer/events', function (Request $request) {
            $request->validate([
                'title' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'description' => 'required|string',
                'location' => 'required|string|max:255',
                'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'start_date' => 'required|date|after:now',
                'end_date' => 'required|date|after:start_date',
                'status' => 'required|in:draft,published',
            ]);

            $bannerPath = null;
            if ($request->hasFile('banner')) {
                $bannerPath = $request->file('banner')->store('events', 'public');
            }

            $slug = Str::slug($request->title) . '-' . Str::random(6);

            $event = \App\Models\Event::create([
                'user_id' => auth()->id(),
                'category_id' => $request->category_id,
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'location' => $request->location,
                'banner' => $bannerPath,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
            ]);

            return redirect()->route('organizer.event.tickets', $event->id)
                ->with('success', 'Event created! Now add tickets for this event.');
        })->name('organizer.event.store');
        
        Route::get('/organizer/events/{id}/edit', function ($id) {
            $event = \App\Models\Event::where('user_id', auth()->id())->findOrFail($id);
            $categories = \App\Models\Category::all();
            return view('dashboard-organizer.event-edit', compact('event', 'categories'));
        })->name('organizer.event.edit');
        
        Route::put('/organizer/events/{id}', function (Request $request, $id) {
            $event = \App\Models\Event::where('user_id', auth()->id())->findOrFail($id);
            
            $request->validate([
                'title' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'description' => 'required|string',
                'location' => 'required|string|max:255',
                'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'status' => 'required|in:draft,published',
            ]);

            if ($request->hasFile('banner')) {
                if ($event->banner && Storage::disk('public')->exists($event->banner)) {
                    Storage::disk('public')->delete($event->banner);
                }
                $event->banner = $request->file('banner')->store('events', 'public');
            }

            $event->update([
                'title' => $request->title,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'location' => $request->location,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
            ]);

            return redirect()->route('organizer.events')->with('success', 'Event updated!');
        })->name('organizer.event.update');
        
        Route::get('/organizer/events/{id}', function ($id) {
            $event = \App\Models\Event::with(['category', 'tickets'])
                ->where('user_id', auth()->id())
                ->findOrFail($id);
            
            $ticketsSold = \App\Models\Eticket::whereHas('ticket', function($q) use ($id) {
                $q->where('event_id', $id);
            })->count();
            
            $revenue = \App\Models\Eticket::whereHas('ticket', function($q) use ($id) {
                $q->where('event_id', $id);
            })->join('tickets', 'etickets.ticket_id', '=', 'tickets.id')
              ->sum('tickets.price');
            
            $tickets = \App\Models\Ticket::where('event_id', $id)->get();
            
            return view('dashboard-organizer.event-detail', compact('event', 'ticketsSold', 'revenue', 'tickets'));
        })->name('organizer.event.detail');
        
        Route::get('/organizer/events/{id}/tickets', function ($id) {
            $event = \App\Models\Event::where('user_id', auth()->id())->findOrFail($id);
            $tickets = \App\Models\Ticket::where('event_id', $id)->get();
            return view('dashboard-organizer.tickets', compact('event', 'tickets'));
        })->name('organizer.event.tickets');
        
        Route::post('/organizer/events/{id}/tickets', function (Request $request, $id) {
            $event = \App\Models\Event::where('user_id', auth()->id())->findOrFail($id);
            
            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|integer|min:0',
                'stock' => 'required|integer|min:1',
                'description' => 'nullable|string',
            ]);

            \App\Models\Ticket::create([
                'event_id' => $id,
                'name' => $request->name,
                'price' => $request->price,
                'stock' => $request->stock,
                'description' => $request->description,
            ]);

            return back()->with('success', 'Ticket added!');
        })->name('organizer.ticket.store');
        
        Route::put('/organizer/events/{eventId}/tickets/{ticketId}', function (Request $request, $eventId, $ticketId) {
            $event = \App\Models\Event::where('user_id', auth()->id())->findOrFail($eventId);
            $ticket = \App\Models\Ticket::where('event_id', $eventId)->findOrFail($ticketId);
            
            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|integer|min:0',
                'stock' => 'required|integer|min:0',
                'description' => 'nullable|string',
            ]);

            $oldStock = $ticket->stock;
            $ticket->update([
                'name' => $request->name,
                'price' => $request->price,
                'stock' => $request->stock,
                'description' => $request->description,
            ]);

            if ($oldStock == 0 && $request->stock > 0) {
                $waitingUsers = \App\Models\WaitingList::where('ticket_id', $ticketId)
                    ->where('status', 'waiting')
                    ->get();
                
                foreach ($waitingUsers as $waiting) {
                    $waiting->update(['status' => 'notified', 'notified_at' => now()]);
                    
                    \App\Models\Notification::create([
                        'user_id' => $waiting->user_id,
                        'title' => 'Tiket Tersedia! 🎫',
                        'message' => 'Tiket "' . $ticket->name . '" untuk event "' . $ticket->event->title . '" sudah tersedia!',
                        'type' => 'success',
                        'link' => route('event.detail', $ticket->event->id),
                        'is_read' => false,
                    ]);
                }
            }

            return back()->with('success', 'Ticket updated!');
        })->name('organizer.ticket.update');
        
        Route::delete('/organizer/events/{eventId}/tickets/{ticketId}', function ($eventId, $ticketId) {
            $event = \App\Models\Event::where('user_id', auth()->id())->findOrFail($eventId);
            $ticket = \App\Models\Ticket::where('event_id', $eventId)->findOrFail($ticketId);
            
            $soldCount = \App\Models\Eticket::where('ticket_id', $ticketId)->count();
            
            if ($soldCount > 0) {
                return back()->with('error', 'Cannot delete ticket that has been sold!');
            }
            
            $ticket->delete();
            return back()->with('success', 'Ticket deleted!');
        })->name('organizer.ticket.delete');
        
        Route::delete('/organizer/events/{id}', function ($id) {
            $event = \App\Models\Event::where('user_id', auth()->id())->findOrFail($id);
            
            $ticketsSold = \App\Models\Eticket::whereHas('ticket', function($q) use ($id) {
                $q->where('event_id', $id);
            })->count();
            
            if ($ticketsSold > 0) {
                return back()->with('error', 'Cannot delete event that has tickets sold!');
            }
            
            if ($event->banner && Storage::disk('public')->exists($event->banner)) {
                Storage::disk('public')->delete($event->banner);
            }
            
            \App\Models\Ticket::where('event_id', $id)->delete();
            $event->delete();
            
            return redirect()->route('organizer.events')->with('success', 'Event deleted!');
        })->name('organizer.event.delete');
        
        Route::get('/organizer/attendees', function () {
            $attendees = \App\Models\Eticket::with(['ticket.event', 'user'])
                ->whereHas('ticket.event', function($q) {
                    $q->where('user_id', auth()->id());
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            return view('dashboard-organizer.attendees', compact('attendees'));
        })->name('organizer.attendees');
        
        Route::get('/organizer/report/export/pdf', function () {
            $events = \App\Models\Event::with('category', 'tickets')
                ->where('user_id', auth()->id())
                ->get();
            
            $eventFinancials = [];
            $totalRevenue = 0;
            $totalTicketsSold = 0;
            
            foreach ($events as $event) {
                $ticketsSold = \App\Models\Eticket::whereHas('ticket', function($q) use ($event) {
                    $q->where('event_id', $event->id);
                })->count();
                
                $revenue = \App\Models\Eticket::whereHas('ticket', function($q) use ($event) {
                    $q->where('event_id', $event->id);
                })->join('tickets', 'etickets.ticket_id', '=', 'tickets.id')
                  ->sum('tickets.price');
                
                $ticketDetails = [];
                foreach ($event->tickets as $ticket) {
                    $sold = \App\Models\Eticket::where('ticket_id', $ticket->id)->count();
                    $ticketDetails[] = [
                        'name' => $ticket->name,
                        'price' => $ticket->price,
                        'stock' => $ticket->stock,
                        'sold' => $sold,
                        'revenue' => $sold * $ticket->price,
                    ];
                }
                
                $eventFinancials[] = [
                    'title' => $event->title,
                    'category' => $event->category->name ?? 'Uncategorized',
                    'start_date' => $event->start_date,
                    'location' => $event->location,
                    'tickets_sold' => $ticketsSold,
                    'revenue' => $revenue,
                    'platform_fee' => $revenue * 0.1,
                    'net_income' => $revenue * 0.9,
                    'ticket_details' => $ticketDetails,
                ];
                
                $totalRevenue += $revenue;
                $totalTicketsSold += $ticketsSold;
            }
            
            $data = [
                'organizer_name' => auth()->user()->name,
                'organizer_email' => auth()->user()->email,
                'generated_at' => now(),
                'events' => $eventFinancials,
                'total_events' => count($eventFinancials),
                'total_tickets_sold' => $totalTicketsSold,
                'total_revenue' => $totalRevenue,
                'total_platform_fee' => $totalRevenue * 0.1,
                'total_net_income' => $totalRevenue * 0.9,
            ];
            
            return view('dashboard-organizer.report-pdf', $data);
        })->name('organizer.report.export.pdf');

        // WAITING LIST MANAGEMENT (ORGANIZER)
        Route::get('/organizer/waitinglist', function () {
            $waitingLists = \App\Models\WaitingList::with(['user', 'ticket.event'])
                ->whereHas('ticket.event', function($q) {
                    $q->where('user_id', auth()->id());
                })
                ->where('status', 'waiting')
                ->orderBy('created_at', 'asc')
                ->paginate(20);
            
            $totalWaiting = \App\Models\WaitingList::whereHas('ticket.event', function($q) {
                $q->where('user_id', auth()->id());
            })->where('status', 'waiting')->count();
            
            return view('dashboard-organizer.waitinglist', compact('waitingLists', 'totalWaiting'));
        })->name('organizer.waitinglist');

        Route::post('/organizer/waitinglist/{ticketId}/notify', function ($ticketId) {
            $ticket = \App\Models\Ticket::whereHas('event', function($q) {
                $q->where('user_id', auth()->id());
            })->findOrFail($ticketId);
            
            $waitingUsers = \App\Models\WaitingList::where('ticket_id', $ticketId)
                ->where('status', 'waiting')
                ->get();
            
            $notifiedCount = 0;
            foreach ($waitingUsers as $waiting) {
                $waiting->update(['status' => 'notified', 'notified_at' => now()]);
                
                \App\Models\Notification::create([
                    'user_id' => $waiting->user_id,
                    'title' => 'Tiket Tersedia! 🎫',
                    'message' => 'Tiket "' . $ticket->name . '" untuk event "' . $ticket->event->title . '" sudah tersedia!',
                    'type' => 'success',
                    'link' => route('event.detail', $ticket->event->id),
                    'is_read' => false,
                ]);
                $notifiedCount++;
            }
            
            return back()->with('success', $notifiedCount . ' orang telah diberi notifikasi.');
        })->name('organizer.waitinglist.notify');

        Route::delete('/organizer/waitinglist/{id}', function ($id) {
            $waiting = \App\Models\WaitingList::whereHas('ticket.event', function($q) {
                $q->where('user_id', auth()->id());
            })->findOrFail($id);
            $waiting->delete();
            return back()->with('success', 'Berhasil dihapus dari waiting list.');
        })->name('organizer.waitinglist.delete');
    });

    // ========================================
    // CUSTOMER DASHBOARD
    // ========================================
    Route::middleware('role:customer')->group(function () {
        
        Route::get('/dashboard', function () {
            $categories = \App\Models\Category::all();
            $pilihanEvents = \App\Models\Event::with(['category', 'tickets'])
                ->where('status', 'published')
                ->where('start_date', '>=', now())
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get();
            
            $semuaEvents = \App\Models\Event::with(['category', 'tickets'])
                ->where('status', 'published')
                ->where('start_date', '>=', now())
                ->orderBy('start_date', 'asc')
                ->get();

            return view('dashboard-customer.customer', compact('categories', 'pilihanEvents', 'semuaEvents'));
        })->name('dashboard');

        // ========================================
        // EVENT LIST
        // ========================================
        Route::get('/events', function () {
            $search = request('search', '');
            $location = request('location', '');
            $categoryId = request('category', '');
            
            $query = \App\Models\Event::with(['category', 'tickets'])
                ->where('status', 'published')
                ->where('start_date', '>=', now());
            
            if (!empty($search)) {
                $query->where('title', 'ilike', '%' . $search . '%');
            }
            
            if (!empty($location)) {
                $query->where('location', 'ilike', '%' . $location . '%');
            }
            
            if (!empty($categoryId)) {
                $query->where('category_id', $categoryId);
            }
            
            $events = $query->orderBy('start_date', 'asc')->paginate(12);
            $categories = \App\Models\Category::all();
            
            if (request()->ajax()) {
                return response()->json($events);
            }
            
            return view('dashboard-customer.events-list', compact('events', 'categories'));
        })->name('events.list');

        Route::get('/events/category/{id}', function ($id) {
            $category = \App\Models\Category::findOrFail($id);
            $events = \App\Models\Event::with(['category', 'tickets'])
                ->where('category_id', $id)
                ->where('status', 'published')
                ->where('start_date', '>=', now())
                ->orderBy('start_date', 'asc')
                ->paginate(12);
            
            $categories = \App\Models\Category::all();
            return view('dashboard-customer.events-list', compact('events', 'categories', 'category'));
        })->name('events.category');

        Route::get('/category/{id}', function ($id) {
            $category = \App\Models\Category::findOrFail($id);
            $events = \App\Models\Event::with(['category', 'tickets'])
                ->where('category_id', $id)
                ->where('status', 'published')
                ->where('start_date', '>=', now())
                ->orderBy('start_date', 'asc')
                ->paginate(12);
            return view('dashboard-customer.category-events', compact('category', 'events'));
        })->name('category.events');

        Route::get('/events/{id}', function ($id) {
            $event = \App\Models\Event::with(['category', 'tickets'])->findOrFail($id);
            $relatedEvents = \App\Models\Event::with(['category', 'tickets'])
                ->where('category_id', $event->category_id)
                ->where('id', '!=', $event->id)
                ->where('status', 'published')
                ->where('start_date', '>=', now())
                ->take(4)
                ->get();
            return view('dashboard-customer.show', compact('event', 'relatedEvents'));
        })->name('event.detail');

        // ========================================
        // CHECKOUT & PAYMENT
        // ========================================
        Route::post('/checkout', function (Request $request) {
            $eventId = $request->event_id;
            $ticketData = $request->tickets;
            
            if (is_null($ticketData)) {
                return back()->with('error', 'Tidak ada tiket yang dipilih!');
            }
            
            $event = \App\Models\Event::findOrFail($eventId);
            
            $selectedTickets = [];
            $totalPrice = 0;
            $totalTickets = 0;

            foreach ($ticketData as $ticketId => $quantity) {
                if ($quantity > 0) {
                    $ticket = \App\Models\Ticket::findOrFail($ticketId);
                    
                    $availableStock = $ticket->stock;
                    if ($availableStock < $quantity) {
                        return back()->with('error', 'Stok tiket "' . $ticket->name . '" tidak mencukupi!');
                    }
                    
                    $subtotal = $ticket->price * $quantity;
                    
                    $selectedTickets[] = [
                        'ticket' => $ticket,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal
                    ];
                    $totalPrice += $subtotal;
                    $totalTickets += $quantity;
                }
            }
            
            if ($totalTickets == 0) {
                return back()->with('error', 'Pilih minimal 1 tiket!');
            }
            
            session([
                'checkout_event_id' => $eventId,
                'checkout_selected_tickets' => $selectedTickets,
                'checkout_total_price' => $totalPrice,
                'checkout_total_tickets' => $totalTickets
            ]);
            
            return redirect()->route('payment.page');
        })->name('checkout.process');

        Route::get('/payment', function () {
            $eventId = session('checkout_event_id');
            $selectedTickets = session('checkout_selected_tickets', []);
            $totalPrice = session('checkout_total_price', 0);
            $totalTickets = session('checkout_total_tickets', 0);
            
            if (!$eventId || empty($selectedTickets)) {
                return redirect()->route('dashboard')->with('error', 'Silakan pilih tiket terlebih dahulu.');
            }
            
            $event = \App\Models\Event::findOrFail($eventId);
            $orderId = 'ORD-' . strtoupper(Str::random(12)) . '-' . time() . '-' . rand(1000, 9999);
            
            return view('dashboard-customer.payment', compact('event', 'selectedTickets', 'totalPrice', 'totalTickets', 'orderId'));
        })->name('payment.page');

        // ========================================
        // CONFIRM PAYMENT (UPLOAD BUKTI)
        // ========================================
        Route::post('/payment/confirm', function (Request $request) {
            try {
                $request->validate([
                    'order_id' => 'required',
                    'amount' => 'required|numeric',
                    'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                    'notes' => 'nullable|string'
                ]);
                
                $proofPath = $request->file('proof_image')->store('payment_proofs', 'public');
                
                $eventId = session('checkout_event_id');
                $selectedTickets = session('checkout_selected_tickets', []);
                $totalPrice = session('checkout_total_price', 0);
                
                if (empty($selectedTickets)) {
                    return response()->json(['success' => false, 'message' => 'Data tiket tidak ditemukan.']);
                }
                
                $transaction = \App\Models\Transaction::create([
                    'user_id' => auth()->id(),
                    'reference_number' => $request->order_id,
                    'total_price' => $totalPrice,
                    'status' => 'pending',
                    'payment_method' => 'bank_transfer',
                ]);
                
                \App\Models\Payment::create([
                    'user_id' => auth()->id(),
                    'transaction_id' => $transaction->id,
                    'order_id' => $request->order_id,
                    'amount' => $request->amount,
                    'proof_image' => $proofPath,
                    'notes' => $request->notes,
                    'status' => 'pending',
                    'expired_at' => now()->addHours(24),
                ]);
                
                // Buat eticket dengan status pending
                foreach ($selectedTickets as $ticketData) {
                    $ticket = \App\Models\Ticket::find($ticketData['ticket']['id']);
                    if ($ticket) {
                        for ($i = 0; $i < $ticketData['quantity']; $i++) {
                            \App\Models\Eticket::create([
                                'transaction_id' => $transaction->id,
                                'ticket_id' => $ticket->id,
                                'user_id' => auth()->id(),
                                'ticket_code' => 'TIX-' . strtoupper(Str::random(8)),
                                'is_scanned' => false,
                            ]);
                        }
                    }
                }
                
                \App\Models\Notification::create([
                    'user_id' => auth()->id(),
                    'title' => 'Bukti Pembayaran Terkirim! 📤',
                    'message' => 'Bukti pembayaran Anda telah terkirim. Tiket akan aktif setelah diverifikasi.',
                    'type' => 'info',
                    'link' => route('my-tickets'),
                    'is_read' => false,
                ]);
                
                session()->forget(['checkout_event_id', 'checkout_selected_tickets', 'checkout_total_price', 'checkout_total_tickets']);
                
                return response()->json(['success' => true, 'message' => 'Bukti pembayaran terkirim!']);
                
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        })->name('payment.confirm');

        // ========================================
        // VERIFIKASI PEMBAYARAN (TESTING)
        // ========================================
        Route::get('/verify-payment/{transactionId}', function ($transactionId) {
            $transaction = \App\Models\Transaction::findOrFail($transactionId);
            
            $transaction->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            
            $payment = \App\Models\Payment::where('transaction_id', $transactionId)->first();
            if ($payment) {
                $payment->update([
                    'status' => 'verified',
                    'verified_at' => now(),
                ]);
            }
            
            $etickets = \App\Models\Eticket::where('transaction_id', $transactionId)->get();
            foreach ($etickets as $eticket) {
                $ticket = \App\Models\Ticket::find($eticket->ticket_id);
                if ($ticket) {
                    $ticket->decrement('stock');
                }
            }
            
            \App\Models\Notification::create([
                'user_id' => $transaction->user_id,
                'title' => 'Pembayaran Diverifikasi! ✅',
                'message' => 'Pembayaran Anda telah diverifikasi. QR Code tiket sudah aktif!',
                'type' => 'success',
                'link' => route('my-tickets'),
                'is_read' => false,
            ]);
            
            return redirect()->route('my-tickets')->with('success', 'Pembayaran berhasil diverifikasi!');
        })->name('payment.verify');

        Route::get('/my-tickets', function () {
            $myTickets = \App\Models\Eticket::with(['ticket.event', 'transaction'])
                ->where('user_id', auth()->id())
                ->latest()
                ->get();
            return view('dashboard-customer.my-tickets', compact('myTickets'));
        })->name('my-tickets');
        
        Route::get('/ticket/{code}/print', function ($code) {
            $eticket = \App\Models\Eticket::with(['ticket.event', 'transaction'])
                ->where('ticket_code', $code)
                ->where('user_id', auth()->id())
                ->firstOrFail();
            return view('dashboard-customer.ticket-print', compact('eticket'));
        })->name('ticket.print');

        // ========================================
        // WAITING LIST (CUSTOMER)
        // ========================================
        Route::post('/waiting-list/{ticketId}/add', function ($ticketId) {
            $ticket = \App\Models\Ticket::find($ticketId);
            
            if (!$ticket) {
                return redirect()->back()->with('error', 'Tiket tidak ditemukan!');
            }
            
            if ($ticket->stock > 0) {
                return redirect()->back()->with('error', 'Tiket masih tersedia, silakan beli langsung!');
            }
            
            $existing = \App\Models\WaitingList::where('user_id', auth()->id())
                ->where('ticket_id', $ticketId)
                ->first();
            
            if ($existing) {
                return redirect()->back()->with('error', 'Anda sudah terdaftar di waiting list!');
            }
            
            \App\Models\WaitingList::create([
                'user_id' => auth()->id(),
                'ticket_id' => $ticketId,
                'quantity' => 1,
                'status' => 'waiting'
            ]);
            
            return redirect()->back()->with('success', 'Berhasil masuk waiting list! Anda akan diberi tahu jika tiket tersedia.');
        })->name('waitinglist.add');

        Route::delete('/waiting-list/{ticketId}/remove', function ($ticketId) {
            $waiting = \App\Models\WaitingList::where('user_id', auth()->id())
                ->where('ticket_id', $ticketId)
                ->first();
            
            if ($waiting) {
                $waiting->delete();
                return redirect()->back()->with('success', 'Berhasil keluar dari waiting list.');
            }
            
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        })->name('waitinglist.remove');
    });

    // ========================================
    // DEBUG ROUTES
    // ========================================
    Route::get('/debug-tickets', function () {
        $user = auth()->user();
        $etickets = \App\Models\Eticket::where('user_id', $user->id)->get();
        $transactions = \App\Models\Transaction::where('user_id', $user->id)->get();
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'etickets_count' => $etickets->count(),
            'etickets' => $etickets,
            'transactions_count' => $transactions->count(),
            'transactions' => $transactions
        ]);
    })->middleware('auth');
});