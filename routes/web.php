<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Organizer\DashboardController;
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
    
    // Redirect berdasarkan role (tetap pakai route dashboard dinamis)
    Route::get('/home', function () {
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
        $user = auth()->user();
        if ($user->role == 'admin') {
            return view('admin.profile');
        } elseif ($user->role == 'organizer') {
            return view('organizer.profile');
        } else {
            return view('customer.profile');
        }
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

        // Upload avatar
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Simpan file baru
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('avatars', $filename, 'public');
            $user->avatar = $path;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->bio = $request->bio;
        
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }
        
        $user->save();
        
        return back()->with('success', 'Profil berhasil diperbarui!');
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
    // DASHBOARD DINAMIS (BERDASARKAN ROLE)
    // ========================================
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $role = $user->role;
        
        if ($role == 'admin') {
            // Redirect ke route admin.dashboard (nanti akan dibuat)
            return app()->make(\App\Http\Controllers\Admin\DashboardController::class)->index(request());
        } elseif ($role == 'organizer') {
            // Panggil controller organizer dashboard
            return app()->make(\App\Http\Controllers\Organizer\DashboardController::class)->index(request());
        } else { // customer
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

            return view('customer.customer', compact('categories', 'pilihanEvents', 'semuaEvents'));
        }
    })->name('dashboard');
    // ========================================
// ORGANIZER 
    // ========================================
    Route::middleware('role:organizer')->prefix('organizer')->name('organizer.')->group(function () {
        Route::post('/upload-avatar', [App\Http\Controllers\Organizer\ProfileController::class, 'uploadAvatar'])->name('upload-avatar');
        Route::get('/events', [App\Http\Controllers\Organizer\EventController::class, 'index'])->name('events.index');
        Route::post('/events/print', [App\Http\Controllers\Organizer\EventController::class, 'print'])->name('events.print');
        Route::get('/export/transactions', [App\Http\Controllers\Organizer\ExportController::class, 'transactions'])->name('export.transactions');
        Route::get('/export/event/{event}', [App\Http\Controllers\Organizer\ExportController::class, 'event'])->name('export.event');
        Route::get('/scan', [App\Http\Controllers\Organizer\ScanController::class, 'index'])->name('scan.index');
        Route::post('/scan', [App\Http\Controllers\Organizer\ScanController::class, 'scan'])->name('scan.process');
        Route::get('/waiting-requests', [App\Http\Controllers\Organizer\WaitingListController::class, 'index'])->name('waiting-requests.index');
        Route::get('/waiting-requests/{event}', [App\Http\Controllers\Organizer\WaitingListController::class, 'show'])->name('waiting-requests.show');
        Route::post('/waiting-requests/{event}/invite', [App\Http\Controllers\Organizer\WaitingListController::class, 'inviteMultiple'])->name('waiting-requests.invite');
        Route::post('/waiting-requests/{request}/cancel', [App\Http\Controllers\Organizer\WaitingListController::class, 'cancel'])->name('waiting-requests.cancel');
    });
    // ========================================
// ADMIN
    // ========================================
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Events
        Route::resource('events', App\Http\Controllers\Admin\EventController::class)->except(['show']);
        Route::post('/events/{event}/approve', [App\Http\Controllers\Admin\EventController::class, 'approve'])->name('events.approve');
        Route::post('/events/{event}/reject', [App\Http\Controllers\Admin\EventController::class, 'reject'])->name('events.reject');
        Route::get('/events/export/csv', [App\Http\Controllers\Admin\EventController::class, 'exportCsv'])->name('events.export.csv');
        Route::get('/events/print', [App\Http\Controllers\Admin\EventController::class, 'printView'])->name('events.print');

        // Categories
        Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->except(['show', 'edit', 'create']);

        // Transactions
        Route::get('/transactions', [App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}', [App\Http\Controllers\Admin\TransactionController::class, 'show'])->name('transactions.show');
        Route::post('/transactions/{transaction}/status', [App\Http\Controllers\Admin\TransactionController::class, 'updateStatus'])->name('transactions.update-status');
        Route::get('/transactions/export/csv', [App\Http\Controllers\Admin\TransactionController::class, 'exportCsv'])->name('transactions.export.csv');

        // Users
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle-suspend', [App\Http\Controllers\Admin\UserController::class, 'toggleSuspend'])->name('users.toggle-suspend');

        // Payment Confirmations
        Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{transaction}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{transaction}/approve', [App\Http\Controllers\Admin\PaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{transaction}/reject', [App\Http\Controllers\Admin\PaymentController::class, 'reject'])->name('payments.reject');
    });

    // ========================================
// CUSTOMER
    // ========================================
    Route::middleware('role:customer')->group(function () {
        // Route /dashboard sudah ditangani di atas, jadi tidak perlu di sini

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
                    'link' => route('organizer.attendees'), // pastikan route ini ada
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

        // REVIEW & RATING
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

        // WAITING LIST
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

        Route::get('/my-waiting-list', function () {
            $waitingLists = \App\Models\WaitingList::with(['ticket.event'])
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            return view('dashboard-customer.waiting-list', compact('waitingLists'));
        })->name('waiting-list.my');

        Route::delete('/waitinglist/{id}/cancel', function ($id) {
            $waiting = \App\Models\WaitingList::where('user_id', auth()->id())
                ->where('status', 'waiting')
                ->findOrFail($id);
            
            $waiting->update(['status' => 'cancelled']);
            
            return back()->with('success', 'Berhasil membatalkan waiting list.');
        })->name('waitinglist.cancel');
    });
});