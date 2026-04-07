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
});

// ============================================
// 3. TERPROTEKSI (Harus Login)
// ============================================
Route::middleware('auth')->group(function () {
    
    // Redirect berdasarkan role
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
    // PROFILE MANAGEMENT (UNTUK SEMUA ROLE)
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
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->bio = $request->bio;
        
        if ($request->filled('new_password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
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
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        
        // Dashboard utama dengan data real
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/events/pending', [App\Http\Controllers\Admin\EventController::class, 'pending'])->name('events.pending');
        Route::post('/events/{event}/approve', [App\Http\Controllers\Admin\EventController::class, 'approve'])->name('events.approve');
        Route::post('/events/{event}/reject', [App\Http\Controllers\Admin\EventController::class, 'reject'])->name('events.reject');
        Route::get('/events', [App\Http\Controllers\Admin\EventController::class, 'index'])->name('events.index');
        Route::get('/events/{event}', [App\Http\Controllers\Admin\EventController::class, 'show'])->name('events.show');
        
        Route::get('/transactions', [App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}', [App\Http\Controllers\Admin\TransactionController::class, 'show'])->name('transactions.show');
        Route::patch('/transactions/{transaction}/status', [App\Http\Controllers\Admin\TransactionController::class, 'updateStatus'])->name('transactions.update-status');
        
        Route::get('/waiting-lists', [App\Http\Controllers\Admin\WaitingListController::class, 'index'])->name('waiting-lists.index');
        Route::get('/waiting-lists/event/{event}', [App\Http\Controllers\Admin\WaitingListController::class, 'byEvent'])->name('waiting-lists.by-event');
        
        // Waiting List Management (tambahan)
        Route::post('/waiting-lists/{waiting}/invite', [App\Http\Controllers\Admin\WaitingListController::class, 'invite'])->name('waiting-lists.invite');
        Route::post('/waiting-lists/event/{event}/invite-next', [App\Http\Controllers\Admin\WaitingListController::class, 'inviteNext'])->name('waiting-lists.invite-next');
        // ==================================================
        // ROUTE UNTUK VIEW SAMPLE (biar ga error karena dipanggil)
        // ==================================================
        Route::get('/charts', function () {
            return view('dashboard-admin.chart');
        })->name('charts');

        Route::get('/forms', function () {
            return view('dashboard-admin.form');
        })->name('forms');

        Route::get('/tables', function () {
            return view('dashboard-admin.table');
        })->name('tables');

        Route::get('/widgets', function () {
            return view('dashboard-admin.widget');
        })->name('widgets');

        Route::get('/elements', function () {
            return view('dashboard-admin.element');
        })->name('elements');

        Route::get('/typography', function () {
            return view('dashboard-admin.typography');
        })->name('typography');

        Route::get('/buttons', function () {
            return view('dashboard-admin.button');
        })->name('buttons');
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
                
                $eventLabels[] = \Illuminate\Support\Str::limit($event->title, 15);
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
        
        Route::post('/organizer/events', function (\Illuminate\Http\Request $request) {
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

            $slug = \Illuminate\Support\Str::slug($request->title) . '-' . \Illuminate\Support\Str::random(6);

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
        
        Route::put('/organizer/events/{id}', function (\Illuminate\Http\Request $request, $id) {
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
                if ($event->banner && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->banner)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($event->banner);
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
        
        Route::post('/organizer/events/{id}/tickets', function (\Illuminate\Http\Request $request, $id) {
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
        
        Route::put('/organizer/events/{eventId}/tickets/{ticketId}', function (\Illuminate\Http\Request $request, $eventId, $ticketId) {
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
                        'message' => 'Tiket "' . $ticket->name . '" untuk event "' . $ticket->event->title . '" sudah tersedia! Segera beli sebelum habis.',
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
            
            if ($event->banner && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->banner)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($event->banner);
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

        // ========================================
        // WAITING LIST MANAGEMENT (ORGANIZER)
        // ========================================
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
                $waiting->update([
                    'status' => 'notified',
                    'notified_at' => now(),
                ]);
                
                \App\Models\Notification::create([
                    'user_id' => $waiting->user_id,
                    'title' => 'Tiket Tersedia! 🎫',
                    'message' => 'Tiket "' . $ticket->name . '" untuk event "' . $ticket->event->title . '" sudah tersedia! Segera beli sebelum habis.',
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

        Route::get('/concerts', function () {
            $concerts = \App\Models\Event::with(['category', 'tickets'])
                ->whereHas('category', function($query) {
                    $query->where('slug', 'like', '%musik%')
                          ->orWhere('name', 'like', '%Konser%')
                          ->orWhere('name', 'like', '%Music%');
                })
                ->where('status', 'published')
                ->where('start_date', '>=', now())
                ->orderBy('start_date', 'asc')
                ->get();
            return view('dashboard-customer.concerts', compact('concerts'));
        })->name('concerts');

        Route::get('/festivals', function () {
            $festivals = \App\Models\Event::with(['category', 'tickets'])
                ->whereHas('category', function($query) {
                    $query->where('slug', 'like', '%festival%')
                          ->orWhere('name', 'like', '%Festival%');
                })
                ->where('status', 'published')
                ->where('start_date', '>=', now())
                ->orderBy('start_date', 'asc')
                ->get();
            return view('dashboard-customer.festivals', compact('festivals'));
        })->name('festivals');

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

        Route::post('/checkout', function (\Illuminate\Http\Request $request) {
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
                    
                    if ($ticket->stock < $quantity) {
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
                'checkout_data' => [
                    'event_id' => $eventId,
                    'event' => $event,
                    'selected_tickets' => $selectedTickets,
                    'total_price' => $totalPrice,
                    'total_tickets' => $totalTickets
                ]
            ]);
            
            return view('dashboard-customer.checkout', compact('event', 'selectedTickets', 'totalPrice', 'totalTickets'));
        })->name('checkout.process');

        Route::post('/payment', function (\Illuminate\Http\Request $request) {
            $eventId = $request->event_id;
            $tickets = $request->tickets;
            $totalPrice = $request->total_price;
            return view('dashboard-customer.payment', compact('eventId', 'tickets', 'totalPrice'));
        })->name('payment.page');

        Route::post('/payment/process', function (\Illuminate\Http\Request $request) {
            $ticketData = $request->tickets;
            
            $totalPrice = 0;
            $eventTitle = '';
            $eventId = null;
            $organizerId = null;
            
            if ($ticketData) {
                foreach ($ticketData as $ticketId => $quantity) {
                    if ($quantity > 0) {
                        $ticket = \App\Models\Ticket::find($ticketId);
                        if ($ticket) {
                            $totalPrice += ($ticket->price * $quantity);
                            $eventTitle = $ticket->event->title;
                            $eventId = $ticket->event->id;
                            $organizerId = $ticket->event->user_id;
                        }
                    }
                }
            }

            $transaction = \App\Models\Transaction::create([
                'user_id' => auth()->id(),
                'total_price' => $totalPrice,
                'status' => 'paid',
                'reference_number' => 'TRX-' . strtoupper(Str::random(10)),
            ]);

            $eticketCount = 0;
            if ($ticketData) {
                foreach ($ticketData as $ticketId => $quantity) {
                    if ($quantity > 0) {
                        $ticket = \App\Models\Ticket::find($ticketId);
                        for ($i = 0; $i < $quantity; $i++) {
                            \App\Models\Eticket::create([
                                'transaction_id' => $transaction->id,
                                'ticket_id' => $ticketId,
                                'user_id' => auth()->id(),
                                'ticket_code' => 'TIX-' . strtoupper(Str::random(8)),
                                'is_scanned' => false,
                            ]);
                            $eticketCount++;
                        }
                        
                        if($ticket && $ticket->stock >= $quantity) {
                            $ticket->decrement('stock', $quantity);
                        }
                    }
                }
            }
            
            \App\Models\Notification::create([
                'user_id' => auth()->id(),
                'title' => 'Pembelian Tiket Berhasil! 🎉',
                'message' => 'Anda telah membeli ' . $eticketCount . ' tiket untuk event "' . $eventTitle . '". Klik untuk lihat tiket Anda.',
                'type' => 'success',
                'link' => route('my-tickets'),
                'is_read' => false,
            ]);
            
            if ($organizerId) {
                \App\Models\Notification::create([
                    'user_id' => $organizerId,
                    'title' => 'Ada Pembelian Tiket Baru! 📢',
                    'message' => auth()->user()->name . ' telah membeli ' . $eticketCount . ' tiket untuk event "' . $eventTitle . '".',
                    'type' => 'info',
                    'link' => route('organizer.attendees'),
                    'is_read' => false,
                ]);
            }
            
            return redirect()->route('my-tickets')->with('success', 'Pembayaran berhasil! ' . $eticketCount . ' E-Ticket sudah terbit.');
        })->name('payment.process');

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
        // REVIEW & RATING
        // ========================================
        Route::post('/event/{id}/review', function ($id, Request $request) {
            $event = \App\Models\Event::findOrFail($id);
            
            $hasPurchased = \App\Models\Eticket::whereHas('ticket', function($q) use ($id) {
                $q->where('event_id', $id);
            })->where('user_id', auth()->id())->exists();
            
            if (!$hasPurchased) {
                return back()->with('error', 'Anda hanya bisa mereview event yang sudah Anda datangi!');
            }
            
            $isEventEnded = \Carbon\Carbon::parse($event->end_date)->isPast();
            if (!$isEventEnded) {
                return back()->with('error', 'Review hanya bisa diberikan setelah event selesai!');
            }
            
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:1000',
            ]);
            
            \App\Models\Review::updateOrCreate(
                ['user_id' => auth()->id(), 'event_id' => $id],
                ['rating' => $request->rating, 'comment' => $request->comment]
            );
            
            $event->avg_rating = \App\Models\Review::where('event_id', $id)->avg('rating');
            $event->total_reviews = \App\Models\Review::where('event_id', $id)->count();
            $event->save();
            
            \App\Models\Notification::create([
                'user_id' => $event->user_id,
                'title' => 'Review Baru untuk Event Anda! ⭐',
                'message' => auth()->user()->name . ' memberi rating ' . $request->rating . '/5 untuk event "' . $event->title . '"',
                'type' => 'info',
                'link' => route('organizer.event.detail', $event->id),
                'is_read' => false,
            ]);
            
            return back()->with('success', 'Terima kasih atas review Anda!');
        })->name('review.store');

        Route::get('/event/{id}/reviews', function ($id) {
            $reviews = \App\Models\Review::with('user')
                ->where('event_id', $id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            return response()->json($reviews);
        })->name('reviews.get');

        // ========================================
        // WAITING LIST (CUSTOMER) - REGULAR
        // ========================================
        Route::post('/waitinglist/{ticketId}/join', function ($ticketId, Request $request) {
            $ticket = \App\Models\Ticket::findOrFail($ticketId);
            $event = $ticket->event;
            $quantity = $request->input('quantity', 1);
            
            $availableStock = $ticket->stock - \App\Models\Eticket::where('ticket_id', $ticketId)->count();
            if ($availableStock > 0) {
                return back()->with('error', 'Tiket masih tersedia! Silakan beli langsung.');
            }
            
            $existing = \App\Models\WaitingList::where('user_id', auth()->id())
                ->where('ticket_id', $ticketId)
                ->whereIn('status', ['waiting', 'notified'])
                ->first();
            
            if ($existing) {
                if ($existing->status == 'waiting') {
                    return back()->with('error', 'Anda sudah terdaftar di waiting list untuk tiket ini!');
                } elseif ($existing->status == 'notified') {
                    return back()->with('info', 'Tiket sudah tersedia! Silakan cek halaman event dan beli sekarang.');
                }
            }
            
            $waiting = \App\Models\WaitingList::create([
                'user_id' => auth()->id(),
                'ticket_id' => $ticketId,
                'event_id' => $event->id,
                'quantity' => $quantity,
                'status' => 'waiting',
                'expires_at' => now()->addDays(7),
            ]);
            
            \App\Models\Notification::create([
                'user_id' => auth()->id(),
                'title' => 'Berhasil Masuk Waiting List! 📝',
                'message' => 'Anda terdaftar untuk ' . $quantity . ' tiket "' . $ticket->name . '" pada event "' . $event->title . '".',
                'type' => 'success',
                'link' => route('my-tickets'),
                'is_read' => false,
            ]);
            
            if ($event->user_id) {
                \App\Models\Notification::create([
                    'user_id' => $event->user_id,
                    'title' => 'Ada yang Masuk Waiting List! 📋',
                    'message' => auth()->user()->name . ' ingin ' . $quantity . ' tiket "' . $ticket->name . '" pada event "' . $event->title . '" (Tiket Habis).',
                    'type' => 'info',
                    'link' => route('organizer.waitinglist'),
                    'is_read' => false,
                ]);
            }
            
            return back()->with('success', 'Berhasil masuk waiting list! Anda akan diberi tahu jika tiket tersedia.');
        })->name('waitinglist.join');

        // ========================================
        // WAITING LIST (CUSTOMER) - AJAX
        // ========================================
        Route::post('/waitinglist/{ticketId}/join-ajax', function ($ticketId, Request $request) {
            $ticket = \App\Models\Ticket::findOrFail($ticketId);
            $event = $ticket->event;
            $quantity = $request->input('quantity', 1);
            
            $availableStock = $ticket->stock - \App\Models\Eticket::where('ticket_id', $ticketId)->count();
            if ($availableStock > 0) {
                return response()->json(['success' => false, 'message' => 'Tiket masih tersedia! Silakan beli langsung.']);
            }
            
            $existing = \App\Models\WaitingList::where('user_id', auth()->id())
                ->where('ticket_id', $ticketId)
                ->whereIn('status', ['waiting', 'notified'])
                ->first();
            
            if ($existing) {
                if ($existing->status == 'waiting') {
                    return response()->json(['success' => false, 'message' => 'Anda sudah terdaftar di waiting list untuk tiket ini!']);
                } elseif ($existing->status == 'notified') {
                    return response()->json(['success' => false, 'message' => 'Tiket sudah tersedia! Silakan cek halaman event dan beli sekarang.']);
                }
            }
            
            $waiting = \App\Models\WaitingList::create([
                'user_id' => auth()->id(),
                'ticket_id' => $ticketId,
                'event_id' => $event->id,
                'quantity' => $quantity,
                'status' => 'waiting',
                'expires_at' => now()->addDays(7),
            ]);
            
            \App\Models\Notification::create([
                'user_id' => auth()->id(),
                'title' => 'Berhasil Masuk Waiting List! 📝',
                'message' => 'Anda terdaftar untuk ' . $quantity . ' tiket "' . $ticket->name . '" pada event "' . $event->title . '".',
                'type' => 'success',
                'link' => route('my-tickets'),
                'is_read' => false,
            ]);
            
            if ($event->user_id) {
                \App\Models\Notification::create([
                    'user_id' => $event->user_id,
                    'title' => 'Ada yang Masuk Waiting List! 📋',
                    'message' => auth()->user()->name . ' ingin ' . $quantity . ' tiket "' . $ticket->name . '" pada event "' . $event->title . '" (Tiket Habis).',
                    'type' => 'info',
                    'link' => route('organizer.waitinglist'),
                    'is_read' => false,
                ]);
            }
            
            return response()->json(['success' => true, 'message' => 'Berhasil masuk waiting list!']);
        })->name('waitinglist.join.ajax');

        // ========================================
        // CANCEL WAITING LIST - AJAX
        // ========================================
        Route::delete('/waitinglist/{ticketId}/cancel-ajax', function ($ticketId) {
            $waiting = \App\Models\WaitingList::where('user_id', auth()->id())
                ->where('ticket_id', $ticketId)
                ->where('status', 'waiting')
                ->first();
            
            if (!$waiting) {
                return response()->json(['success' => false, 'message' => 'Waiting list tidak ditemukan.']);
            }
            
            $waiting->update(['status' => 'cancelled']);
            
            return response()->json(['success' => true, 'message' => 'Berhasil membatalkan waiting list.']);
        })->name('waitinglist.cancel.ajax');

        // ========================================
        // MY WAITING LIST PAGE
        // ========================================
        Route::get('/my-waiting-list', function () {
            $waitingLists = \App\Models\WaitingList::with(['ticket.event'])
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            return view('dashboard-customer.waiting-list', compact('waitingLists'));
        })->name('waiting-list.my');

        // ========================================
        // CANCEL WAITING LIST - REGULAR
        // ========================================
        Route::delete('/waitinglist/{id}/cancel', function ($id) {
            $waiting = \App\Models\WaitingList::where('user_id', auth()->id())
                ->where('status', 'waiting')
                ->findOrFail($id);
            
            $waiting->update(['status' => 'cancelled']);
            
            return back()->with('success', 'Berhasil membatalkan waiting list.');
        })->name('waitinglist.cancel');
    });
});