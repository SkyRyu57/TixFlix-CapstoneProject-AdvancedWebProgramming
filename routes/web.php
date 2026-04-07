<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

// 1. HALAMAN UTAMA
Route::get('/', function () {
    return view('welcome');
});

// 2. AUTHENTICATION (GUEST)
Route::middleware('guest')->group(function () {
    Route::get('/register', function () { return view('auth.register'); })->name('register');
    Route::get('/login', function () { return view('auth.login'); })->name('login');
    
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
});

// 3. TERPROTEKSI (Harus Login)
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

    // ORGANIZER DASHBOARD
    Route::middleware('role:organizer')->group(function () {
        Route::get('/organizer/dashboard', function () {
            return "Halo Organizer! Kelola event di sini. <br> <form action='".route('logout')."' method='POST'>".csrf_field()."<button type='submit'>Logout Resmi</button></form>";
        })->name('organizer.dashboard');
    });

    // CUSTOMER DASHBOARD
    Route::middleware('role:customer')->group(function () {
        
        // Dashboard Utama
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

        // Concerts
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

        // Festivals
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

        // Category Events (BARU)
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

        // Detail Event
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

        // Checkout
        Route::post('/checkout', function (\Illuminate\Http\Request $request) {
            $eventId = $request->event_id;
            $ticketData = $request->tickets;
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
            
            return view('dashboard-customer.checkout', compact('event', 'selectedTickets', 'totalPrice', 'totalTickets'));
        })->name('checkout.process');

        // Payment Page
        Route::post('/payment', function (\Illuminate\Http\Request $request) {
            $eventId = $request->event_id;
            $tickets = $request->tickets;
            $totalPrice = $request->total_price;
            
            return view('dashboard-customer.payment', compact('eventId', 'tickets', 'totalPrice'));
        })->name('payment.page');

        // Process Payment
        Route::post('/payment/process', function (\Illuminate\Http\Request $request) {
            $ticketData = $request->tickets;
            
            $totalPrice = 0;
            if ($ticketData) {
                foreach ($ticketData as $ticketId => $quantity) {
                    if ($quantity > 0) {
                        $ticket = \App\Models\Ticket::find($ticketId);
                        if ($ticket) {
                            $totalPrice += ($ticket->price * $quantity);
                        }
                    }
                }
            }

            // Simpan Transaksi
            $transaction = \App\Models\Transaction::create([
                'user_id' => auth()->id(),
                'total_price' => $totalPrice,
                'status' => 'paid',
                'reference_number' => 'TRX-' . strtoupper(Str::random(10)),
            ]);

            // Simpan E-Ticket
            if ($ticketData) {
                foreach ($ticketData as $ticketId => $quantity) {
                    if ($quantity > 0) {
                        for ($i = 0; $i < $quantity; $i++) {
                            \App\Models\Eticket::create([
                                'transaction_id' => $transaction->id,
                                'ticket_id' => $ticketId,
                                'user_id' => auth()->id(),
                                'ticket_code' => 'TIX-' . strtoupper(Str::random(8)),
                                'is_scanned' => false,
                            ]);
                        }
                        
                        $ticket = \App\Models\Ticket::find($ticketId);
                        if($ticket && $ticket->stock >= $quantity) {
                            $ticket->decrement('stock', $quantity);
                        }
                    }
                }
            }
            
            return redirect()->route('my-tickets')->with('success', 'Pembayaran berhasil! E-Ticket kamu sudah terbit.');
        })->name('payment.process');

        // My Tickets
        Route::get('/my-tickets', function () {
            $myTickets = \App\Models\Eticket::with(['ticket.event'])
                ->where('user_id', auth()->id())
                ->latest()
                ->get();
            
            return view('dashboard-customer.my-tickets', compact('myTickets'));
        })->name('my-tickets');
    });
});