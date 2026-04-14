<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
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
    
    // Forgot Password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
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
        Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{transaction}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{transaction}/approve', [App\Http\Controllers\Admin\PaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{transaction}/reject', [App\Http\Controllers\Admin\PaymentController::class, 'reject'])->name('payments.reject');
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
            
            if (!$eventId || empty($selectedTickets)) {
                return redirect()->route('dashboard')->with('error', 'Silakan pilih tiket terlebih dahulu.');
            }
            
            $event = \App\Models\Event::findOrFail($eventId);
            
            // Cek apakah sudah ada transaksi pending untuk user ini dengan event yang sama
            $existingTransaction = \App\Models\Transaction::where('user_id', auth()->id())
                ->where('status', 'pending')
                ->where('created_at', '>=', now()->subMinutes(5))
                ->first();
            
            if ($existingTransaction) {
                $orderId = $existingTransaction->reference_number;
                $transaction = $existingTransaction;
            } else {
                $orderId = 'ORD-' . strtoupper(Str::random(12)) . '-' . time() . '-' . rand(1000, 9999);
                
                // Buat transaksi baru dengan expires_at 5 menit
                $transaction = \App\Models\Transaction::create([
                    'user_id' => auth()->id(),
                    'reference_number' => $orderId,
                    'total_price' => $totalPrice,
                    'status' => 'pending',
                    'payment_method' => 'bank_transfer',
                    'expires_at' => now()->addMinutes(5),
                ]);
                
                // Simpan data tiket di session (atau bisa juga di tabel terpisah)
                session(['payment_selected_tickets' => $selectedTickets]);
                
                // Kirim notifikasi ke customer
                \App\Models\Notification::create([
                    'user_id' => auth()->id(),
                    'title' => '⏳ Segera Selesaikan Pembayaran',
                    'message' => 'Anda memiliki waktu 5 menit untuk menyelesaikan pembayaran tiket ' . $event->title . '. Jangan sampai kadaluarsa!',
                    'type' => 'warning',
                    'link' => route('payment.page'),
                    'is_read' => false,
                ]);
            }
            
            return view('customer.payment', compact('event', 'selectedTickets', 'totalPrice', 'totalTickets', 'orderId', 'transaction'));
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
                
                // Cari transaksi berdasarkan reference_number
                $transaction = \App\Models\Transaction::where('reference_number', $request->order_id)
                    ->where('user_id', auth()->id())
                    ->first();
                
                if (!$transaction) {
                    return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.']);
                }
                
                // Cek apakah transaksi sudah kadaluarsa
                if ($transaction->expires_at && now()->greaterThan($transaction->expires_at)) {
                    $transaction->update(['status' => 'expired']);
                    return response()->json(['success' => false, 'message' => 'Waktu pembayaran telah habis. Silakan pesan ulang.']);
                }
                
                // Ambil data tiket dari session (atau dari relasi jika sudah ada)
                $selectedTickets = session('payment_selected_tickets', []);
                
                // Jika transaksi belum memiliki e-tickets, buat
                $existingEtickets = \App\Models\Eticket::where('transaction_id', $transaction->id)->count();
                if ($existingEtickets == 0 && !empty($selectedTickets)) {
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
                }
                
                // Simpan bukti pembayaran
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
                
                // Jangan ubah status transaksi, tetap pending sampai admin approve
                
                \App\Models\Notification::create([
                    'user_id' => auth()->id(),
                    'title' => 'Bukti Pembayaran Terkirim! 📤',
                    'message' => 'Bukti pembayaran Anda telah terkirim. Tiket akan aktif setelah diverifikasi.',
                    'type' => 'info',
                    'link' => route('my-tickets'),
                    'is_read' => false,
                ]);
                
                session()->forget(['checkout_event_id', 'checkout_selected_tickets', 'checkout_total_price', 'checkout_total_tickets', 'payment_selected_tickets']);
                
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
        Route::get('/pesanan-perlu-dibayar', function () {
            $waitingRequests = \App\Models\WaitingRequest::where('user_id', auth()->id())
                ->where('status', 'invited')
                ->where(function($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $waitingItems = [];
            foreach ($waitingRequests as $wr) {
                $ticket = \App\Models\Ticket::where('event_id', $wr->event_id)->first();
                if (!$ticket) continue;
                $event = \App\Models\Event::find($wr->event_id);
                if (!$event) continue;
                $waitingItems[] = (object) [
                    'id' => $wr->id,
                    'event_title' => $event->title,
                    'event_start_date' => $event->start_date,
                    'event_location' => $event->location,
                    'ticket_name' => $ticket->name,
                    'ticket_price' => $ticket->price,
                    'quantity' => $wr->quantity,
                    'expires_at' => $wr->expires_at,
                ];
            }

            return view('customer.waiting-list', compact('waitingItems'));
        })->name('customer.waiting-list');

        Route::post('/waiting-list/{ticketId}/add', function ($ticketId) {
            $ticket = \App\Models\Ticket::find($ticketId);
            if (!$ticket) return back()->with('error', 'Tiket tidak ditemukan!');
            if ($ticket->stock > 0) return back()->with('error', 'Tiket masih tersedia, silakan beli langsung!');
            
            $existing = \App\Models\WaitingRequest::where('user_id', auth()->id())
                        ->where('ticket_id', $ticketId)
                        ->first();
            if ($existing) return back()->with('error', 'Anda sudah terdaftar di waiting list!');
            
            $waiting = \App\Models\WaitingRequest::create([
                'user_id' => auth()->id(),
                'ticket_id' => $ticketId,
                'event_id' => $ticket->event_id,
                'quantity' => 1,
                'status' => 'pending',
                'expires_at' => now()->addDays(7),
            ]);
            
            // Notifikasi ke organizer
            $event = $ticket->event;
            \App\Models\Notification::create([
                'user_id' => $event->user_id,
                'title' => 'Ada yang Masuk Waiting List',
                'message' => auth()->user()->name . ' ingin 1 tiket ' . $ticket->name,
                'type' => 'info',
                'link' => route('organizer.waiting-requests.index'),
                'is_read' => false,
            ]);
            
            return back()->with('success', 'Berhasil masuk waiting list! Anda akan diberi tahu jika tiket tersedia.');
        })->name('waitinglist.add');

        Route::delete('/waiting-list/{ticketId}/remove', function ($ticketId) {
            $waiting = \App\Models\WaitingRequest::where('user_id', auth()->id())->where('ticket_id', $ticketId)->first();
            if ($waiting) {
                $waiting->delete();
                return redirect()->back()->with('success', 'Berhasil keluar dari waiting list.');
            }
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        })->name('waitinglist.remove');

        Route::get('/my-waiting-list', function () {
            $waitingLists = \App\Models\WaitingRequest::with(['ticket.event'])->where('user_id', auth()->id())->orderBy('created_at', 'desc')->paginate(10);
            return view('customer.waiting-list', compact('waitingLists'));
        })->name('waiting-list.my');

        Route::get('/waiting-checkout/{waitingRequest}', function ($waitingRequestId) {
            $wr = \App\Models\WaitingRequest::where('user_id', auth()->id())->findOrFail($waitingRequestId);
            
            if ($wr->status !== 'invited') {
                return redirect()->route('my-tickets')->with('error', 'Undangan tidak valid.');
            }
            if ($wr->expires_at && now()->greaterThan($wr->expires_at)) {
                $wr->update(['status' => 'expired']);
                return redirect()->route('my-tickets')->with('error', 'Waktu pembayaran telah habis.');
            }
            
            // Ambil tiket (jika null, cari dari event)
            $ticket = $wr->ticket ?? \App\Models\Ticket::where('event_id', $wr->event_id)->first();
            if (!$ticket) {
                return redirect()->route('my-tickets')->with('error', 'Tiket tidak ditemukan.');
            }
            
            $event = $ticket->event;
            $quantity = $wr->quantity;
            $totalPrice = $ticket->price * $quantity;
            $orderId = 'ORD-' . strtoupper(Str::random(12)) . '-' . time() . '-' . rand(1000, 9999);
            
            // Definisikan variabel yang akan dikirim ke view
            $selectedTickets = [
                [
                    'ticket' => $ticket,
                    'quantity' => $quantity,
                    'subtotal' => $totalPrice,
                ]
            ];
            $totalTickets = $quantity;
            
            session([
                'checkout_event_id' => $event->id,
                'checkout_selected_tickets' => $selectedTickets,
                'checkout_total_price' => $totalPrice,
                'checkout_total_tickets' => $totalTickets,
                'checkout_waiting_request_id' => $wr->id,
            ]);
            
            return view('customer.payment', compact('event', 'selectedTickets', 'totalPrice', 'totalTickets', 'orderId'));
        })->name('waiting.checkout');
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