<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
    
    // Forgot Password
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');
    
    Route::post('/check-email', function (Request $request) {
        $email = $request->email;
        $user = \App\Models\User::where('email', $email)->first();
        if ($user) {
            return response()->json(['exists' => true, 'user_name' => $user->name, 'user_email' => $user->email]);
        }
        return response()->json(['exists' => false]);
    })->name('check.email');
    
    Route::post('/create-reset-token', function (Request $request) {
        $email = $request->email;
        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email tidak ditemukan']);
        }
        $token = Str::random(64);
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => $token, 'created_at' => now()]
        );
        $resetLink = url('/reset-password/' . $token . '?email=' . urlencode($email));
        return response()->json(['success' => true, 'reset_link' => $resetLink, 'token' => $token]);
    })->name('create.reset.token');
    
    Route::get('/reset-password/{token}', function ($token) {
        $resetRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('token', $token)->first();
        if (!$resetRecord) {
            return redirect()->route('password.request')->with('error', 'Token tidak valid atau sudah kadaluarsa!');
        }
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('token', $token)->delete();
            return redirect()->route('password.request')->with('error', 'Link reset password sudah kadaluarsa!');
        }
        return view('auth.reset-password', ['token' => $token, 'email' => $resetRecord->email]);
    })->name('password.reset');
    
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
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        return redirect()->route('login')->with('success', 'Password berhasil direset!');
    })->name('password.update');
});

// ============================================
// 3. TERPROTEKSI (Harus Login)
// ============================================
Route::middleware('auth')->group(function () {
    
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/logout', function () {
        return redirect()->route('home');
    });

    // Profile Management
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
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
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

    // Notifications
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
        return response()->json(['notifications' => $notifications, 'unread_count' => $unreadCount]);
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

    // Dashboard dinamis berdasarkan role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $role = $user->role;
        if ($role == 'admin') {
            return app()->make(\App\Http\Controllers\Admin\DashboardController::class)->index(request());
        } elseif ($role == 'organizer') {
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
    // ORGANIZER ROUTES (branch kita)
    // ========================================
    Route::middleware('role:organizer')->prefix('organizer')->name('organizer.')->group(function () {
        Route::post('/upload-avatar', [App\Http\Controllers\Organizer\ProfileController::class, 'uploadAvatar'])->name('upload-avatar');
        Route::get('/events', [App\Http\Controllers\Organizer\EventController::class, 'index'])->name('events.index');
        Route::post('/events/print', [App\Http\Controllers\Organizer\EventController::class, 'print'])->name('events.print');
        Route::get('/export/transactions', [App\Http\Controllers\Organizer\ExportController::class, 'transactions'])->name('export.transactions');
        Route::get('/export/event/{event}', [App\Http\Controllers\Organizer\ExportController::class, 'event'])->name('export.event');
        Route::get('/scan', [App\Http\Controllers\Organizer\ScanController::class, 'index'])->name('scan.index');
        Route::post('/scan', [App\Http\Controllers\Organizer\ScanController::class, 'scan'])->name('scan.process');
        // Waiting Requests (organizer)
        Route::get('/waiting-requests', [App\Http\Controllers\Organizer\WaitingListController::class, 'index'])->name('waiting-requests.index');
        Route::get('/waiting-requests/{event}', [App\Http\Controllers\Organizer\WaitingListController::class, 'show'])->name('waiting-requests.show');
        Route::post('/waiting-requests/{event}/invite', [App\Http\Controllers\Organizer\WaitingListController::class, 'inviteMultiple'])->name('waiting-requests.invite');
        Route::post('/waiting-requests/{request}/cancel', [App\Http\Controllers\Organizer\WaitingListController::class, 'cancel'])->name('waiting-requests.cancel');
    });

    // ========================================
    // ADMIN ROUTES
    // ========================================
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('events', App\Http\Controllers\Admin\EventController::class)->except(['show']);
        Route::post('/events/{event}/approve', [App\Http\Controllers\Admin\EventController::class, 'approve'])->name('events.approve');
        Route::post('/events/{event}/reject', [App\Http\Controllers\Admin\EventController::class, 'reject'])->name('events.reject');
        Route::get('/events/export/csv', [App\Http\Controllers\Admin\EventController::class, 'exportCsv'])->name('events.export.csv');
        Route::get('/events/print', [App\Http\Controllers\Admin\EventController::class, 'printView'])->name('events.print');
        Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->except(['show', 'edit', 'create']);
        Route::get('/transactions', [App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}', [App\Http\Controllers\Admin\TransactionController::class, 'show'])->name('transactions.show');
        Route::post('/transactions/{transaction}/status', [App\Http\Controllers\Admin\TransactionController::class, 'updateStatus'])->name('transactions.update-status');
        Route::get('/transactions/export/csv', [App\Http\Controllers\Admin\TransactionController::class, 'exportCsv'])->name('transactions.export.csv');
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle-suspend', [App\Http\Controllers\Admin\UserController::class, 'toggleSuspend'])->name('users.toggle-suspend');
        
        // ==================== PERBAIKAN PAYMENTS ====================
        // Menggunakan PaymentController dengan method yang benar
        Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/approve', [App\Http\Controllers\Admin\PaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{payment}/reject', [App\Http\Controllers\Admin\PaymentController::class, 'reject'])->name('payments.reject');
    });

    // ========================================
    // CUSTOMER ROUTES
    // ========================================
    Route::middleware('role:customer')->group(function () {
        // Dashboard customer sudah ditangani oleh /dashboard di atas

        Route::get('/events', function () {
            $search = request('search', '');
            $location = request('location', '');
            $categoryId = request('category', '');
            $query = \App\Models\Event::with(['category', 'tickets'])
                ->where('status', 'published')
                ->where('start_date', '>=', now());
            if (!empty($search)) $query->where('title', 'ilike', '%' . $search . '%');
            if (!empty($location)) $query->where('location', 'ilike', '%' . $location . '%');
            if (!empty($categoryId)) $query->where('category_id', $categoryId);
            $events = $query->orderBy('start_date', 'asc')->paginate(12);
            $categories = \App\Models\Category::all();
            if (request()->ajax()) return response()->json($events);
            return view('customer.events-list', compact('events', 'categories'));
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
            return view('customer.events-list', compact('events', 'categories', 'category'));
        })->name('events.category');

        Route::get('/category/{id}', function ($id) {
            $category = \App\Models\Category::findOrFail($id);
            $events = \App\Models\Event::with(['category', 'tickets'])
                ->where('category_id', $id)
                ->where('status', 'published')
                ->where('start_date', '>=', now())
                ->orderBy('start_date', 'asc')
                ->paginate(12);
            return view('customer.category-events', compact('category', 'events'));
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
            return view('customer.show', compact('event', 'relatedEvents'));
        })->name('event.detail');

        Route::post('/checkout', function (Request $request) {
            $eventId = $request->event_id;
            $ticketData = $request->tickets;
            if (is_null($ticketData)) return back()->with('error', 'Tidak ada tiket yang dipilih!');
            $event = \App\Models\Event::findOrFail($eventId);
            $selectedTickets = [];
            $totalPrice = 0;
            $totalTickets = 0;
            foreach ($ticketData as $ticketId => $quantity) {
                if ($quantity > 0) {
                    $ticket = \App\Models\Ticket::findOrFail($ticketId);
                    if ($ticket->stock < $quantity) return back()->with('error', 'Stok tiket "' . $ticket->name . '" tidak mencukupi!');
                    $subtotal = $ticket->price * $quantity;
                    $selectedTickets[] = ['ticket' => $ticket, 'quantity' => $quantity, 'subtotal' => $subtotal];
                    $totalPrice += $subtotal;
                    $totalTickets += $quantity;
                }
            }
            if ($totalTickets == 0) return back()->with('error', 'Pilih minimal 1 tiket!');
            session(['checkout_event_id' => $eventId, 'checkout_selected_tickets' => $selectedTickets, 'checkout_total_price' => $totalPrice, 'checkout_total_tickets' => $totalTickets]);
            return redirect()->route('payment.page');
        })->name('checkout.process');

        Route::get('/payment', function () {
            $eventId = session('checkout_event_id');
            $selectedTickets = session('checkout_selected_tickets', []);
            $totalPrice = session('checkout_total_price', 0);
            $totalTickets = session('checkout_total_tickets', 0);
            if (!$eventId || empty($selectedTickets)) return redirect()->route('dashboard')->with('error', 'Silakan pilih tiket terlebih dahulu.');
            $event = \App\Models\Event::findOrFail($eventId);
            $orderId = 'ORD-' . strtoupper(Str::random(12)) . '-' . time() . '-' . rand(1000, 9999);
            return view('customer.payment', compact('event', 'selectedTickets', 'totalPrice', 'totalTickets', 'orderId'));
        })->name('payment.page');

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
                if (empty($selectedTickets)) return response()->json(['success' => false, 'message' => 'Data tiket tidak ditemukan.']);
                $transaction = \App\Models\Transaction::create(['user_id' => auth()->id(), 'reference_number' => $request->order_id, 'total_price' => $totalPrice, 'status' => 'pending', 'payment_method' => 'bank_transfer']);
                \App\Models\Payment::create(['user_id' => auth()->id(), 'transaction_id' => $transaction->id, 'order_id' => $request->order_id, 'amount' => $request->amount, 'proof_image' => $proofPath, 'notes' => $request->notes, 'status' => 'pending', 'expired_at' => now()->addHours(24)]);
                foreach ($selectedTickets as $ticketData) {
                    $ticket = \App\Models\Ticket::find($ticketData['ticket']['id']);
                    if ($ticket) {
                        for ($i = 0; $i < $ticketData['quantity']; $i++) {
                            \App\Models\Eticket::create(['transaction_id' => $transaction->id, 'ticket_id' => $ticket->id, 'user_id' => auth()->id(), 'ticket_code' => 'TIX-' . strtoupper(Str::random(8)), 'is_scanned' => false]);
                        }
                    }
                }
                \App\Models\Notification::create(['user_id' => auth()->id(), 'title' => 'Bukti Pembayaran Terkirim! 📤', 'message' => 'Bukti pembayaran Anda telah terkirim. Tiket akan aktif setelah diverifikasi.', 'type' => 'info', 'link' => route('my-tickets'), 'is_read' => false]);
                session()->forget(['checkout_event_id', 'checkout_selected_tickets', 'checkout_total_price', 'checkout_total_tickets']);
                return response()->json(['success' => true, 'message' => 'Bukti pembayaran terkirim!']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        })->name('payment.confirm');

        Route::get('/verify-payment/{transactionId}', function ($transactionId) {
            $transaction = \App\Models\Transaction::findOrFail($transactionId);
            $transaction->update(['status' => 'paid', 'paid_at' => now()]);
            $payment = \App\Models\Payment::where('transaction_id', $transactionId)->first();
            if ($payment) $payment->update(['status' => 'verified', 'verified_at' => now()]);
            $etickets = \App\Models\Eticket::where('transaction_id', $transactionId)->get();
            foreach ($etickets as $eticket) {
                $ticket = \App\Models\Ticket::find($eticket->ticket_id);
                if ($ticket) $ticket->decrement('stock');
            }
            \App\Models\Notification::create(['user_id' => $transaction->user_id, 'title' => 'Pembayaran Diverifikasi! ✅', 'message' => 'Pembayaran Anda telah diverifikasi. QR Code tiket sudah aktif!', 'type' => 'success', 'link' => route('my-tickets'), 'is_read' => false]);
            return redirect()->route('my-tickets')->with('success', 'Pembayaran berhasil diverifikasi!');
        })->name('payment.verify');

        Route::get('/my-tickets', function () {
            $myTickets = \App\Models\Eticket::with(['ticket.event', 'transaction'])->where('user_id', auth()->id())->latest()->get();
            return view('customer.my-tickets', compact('myTickets'));
        })->name('my-tickets');
        
        Route::get('/ticket/{code}/print', function ($code) {
            $eticket = \App\Models\Eticket::with(['ticket.event', 'transaction'])->where('ticket_code', $code)->where('user_id', auth()->id())->firstOrFail();
            return view('customer.ticket-print', compact('eticket'));
        })->name('ticket.print');

        // Waiting List Customer
        Route::post('/waiting-list/{ticketId}/add', function ($ticketId) {
            $ticket = \App\Models\Ticket::find($ticketId);
            if (!$ticket) return redirect()->back()->with('error', 'Tiket tidak ditemukan!');
            if ($ticket->stock > 0) return redirect()->back()->with('error', 'Tiket masih tersedia, silakan beli langsung!');
            $existing = \App\Models\WaitingList::where('user_id', auth()->id())->where('ticket_id', $ticketId)->first();
            if ($existing) return redirect()->back()->with('error', 'Anda sudah terdaftar di waiting list!');
            \App\Models\WaitingList::create(['user_id' => auth()->id(), 'ticket_id' => $ticketId, 'quantity' => 1, 'status' => 'waiting']);
            return redirect()->back()->with('success', 'Berhasil masuk waiting list! Anda akan diberi tahu jika tiket tersedia.');
        })->name('waitinglist.add');

        Route::delete('/waiting-list/{ticketId}/remove', function ($ticketId) {
            $waiting = \App\Models\WaitingList::where('user_id', auth()->id())->where('ticket_id', $ticketId)->first();
            if ($waiting) {
                $waiting->delete();
                return redirect()->back()->with('success', 'Berhasil keluar dari waiting list.');
            }
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        })->name('waitinglist.remove');

        Route::get('/my-waiting-list', function () {
            $waitingLists = \App\Models\WaitingList::with(['ticket.event'])->where('user_id', auth()->id())->orderBy('created_at', 'desc')->paginate(10);
            return view('customer.waiting-list', compact('waitingLists'));
        })->name('waiting-list.my');

        // Review & Rating
        Route::post('/event/{id}/review', function ($id, Request $request) {
            $event = \App\Models\Event::findOrFail($id);
            $hasPurchased = \App\Models\Eticket::whereHas('ticket', function($q) use ($id) { $q->where('event_id', $id); })->where('user_id', auth()->id())->exists();
            if (!$hasPurchased) return back()->with('error', 'Anda hanya bisa mereview event yang sudah Anda datangi!');
            $isEventEnded = \Carbon\Carbon::parse($event->end_date)->isPast();
            if (!$isEventEnded) return back()->with('error', 'Review hanya bisa diberikan setelah event selesai!');
            $request->validate(['rating' => 'required|integer|min:1|max:5', 'comment' => 'nullable|string|max:1000']);
            \App\Models\Review::updateOrCreate(['user_id' => auth()->id(), 'event_id' => $id], ['rating' => $request->rating, 'comment' => $request->comment]);
            $event->avg_rating = \App\Models\Review::where('event_id', $id)->avg('rating');
            $event->total_reviews = \App\Models\Review::where('event_id', $id)->count();
            $event->save();
            \App\Models\Notification::create(['user_id' => $event->user_id, 'title' => 'Review Baru untuk Event Anda! ⭐', 'message' => auth()->user()->name . ' memberi rating ' . $request->rating . '/5 untuk event "' . $event->title . '"', 'type' => 'info', 'link' => route('organizer.event.detail', $event->id), 'is_read' => false]);
            return back()->with('success', 'Terima kasih atas review Anda!');
        })->name('review.store');

        Route::get('/event/{id}/reviews', function ($id) {
            $reviews = \App\Models\Review::with('user')->where('event_id', $id)->orderBy('created_at', 'desc')->paginate(10);
            return response()->json($reviews);
        })->name('reviews.get');
    });
});

// Debug routes
Route::get('/debug-tickets', function () {
    $user = auth()->user();
    $etickets = \App\Models\Eticket::where('user_id', $user->id)->get();
    $transactions = \App\Models\Transaction::where('user_id', $user->id)->get();
    return response()->json([
        'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        'etickets_count' => $etickets->count(),
        'etickets' => $etickets,
        'transactions_count' => $transactions->count(),
        'transactions' => $transactions
    ]);
})->middleware('auth');