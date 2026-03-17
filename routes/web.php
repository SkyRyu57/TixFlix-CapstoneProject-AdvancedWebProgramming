<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\CustomerController;
use Illuminate\Support\Str;

// 1. HALAMAN UTAMA
Route::get('/', function () {
    return view('welcome');
});

// 2. AUTHENTICATION (GUEST - Hanya bisa diakses jika BELUM login)
Route::middleware('guest')->group(function () {
    Route::get('/register', function () { return view('auth.register'); })->name('register');
    Route::get('/login', function () { return view('auth.login'); })->name('login');
    
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
});

// 3. TERPROTEKSI (Harus Login)
Route::middleware('auth')->group(function () {
    
    // --- PUSAT KENDALI REDIRECT (PENTING) ---
    Route::get('/home', function () {
        $role = auth()->user()->role;
        if ($role == 'admin') return redirect()->route('admin.dashboard');
        if ($role == 'organizer') return redirect()->route('organizer.dashboard');
        return redirect()->route('dashboard'); // Untuk customer
    })->name('home');

    // --- PROSES KELUAR (LOGOUT) ---
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/logout', function () {
        return redirect()->route('home');
    });

    // --- DASHBOARD: ADMIN ---
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return "Halo Admin! Ini Dashboard kamu. <br> <form action='".route('logout')."' method='POST'>".csrf_field()."<button type='submit'>Logout Resmi</button></form>";
        })->name('admin.dashboard');
    });

    // --- DASHBOARD: ORGANIZER ---
    Route::middleware('role:organizer')->group(function () {
        Route::get('/organizer/dashboard', function () {
            return "Halo Organizer! Kelola event di sini. <br> <form action='".route('logout')."' method='POST'>".csrf_field()."<button type='submit'>Logout Resmi</button></form>";
        })->name('organizer.dashboard');
    });

    // --- DASHBOARD: CUSTOMER ---
    Route::middleware('role:customer')->group(function () {
        
        // Menampilkan Dashboard Utama (Beranda, Trending, Semua Event)
        Route::get('/dashboard', function () {
            $categories = \App\Models\Category::all(); // <-- TAMBAHAN: Tarik semua kategori
            $pilihanEvents = \App\Models\Event::with(['category', 'tickets'])->where('status', 'published')->take(4)->get(); // <-- Tarik relasi tiket buat harga
            $semuaEvents = \App\Models\Event::with(['category', 'tickets'])->where('status', 'published')->orderBy('start_date', 'asc')->get();

            return view('dashboard-customer.customer', compact('categories', 'pilihanEvents', 'semuaEvents'));
        })->name('dashboard');

        // --- Rute untuk Navbar (Dinamis dari Database) ---
        Route::get('/concerts', function () {
            $concerts = \App\Models\Event::with('category')
                ->whereHas('category', function($query) {
                    $query->where('slug', 'like', '%musik%');
                })
                ->where('status', 'published')
                ->orderBy('start_date', 'asc')
                ->get();
            return view('dashboard-customer.concerts', compact('concerts'));
        })->name('concerts');

        Route::get('/festivals', function () {
            $festivals = \App\Models\Event::with('category')
                ->whereHas('category', function($query) {
                    $query->where('slug', 'like', '%festival%');
                })
                ->where('status', 'published')
                ->orderBy('start_date', 'asc')
                ->get();
            return view('dashboard-customer.festivals', compact('festivals'));
        })->name('festivals');
        // -----------------------------------------

        // Menampilkan Halaman Detail Event
        Route::get('/events/{id}', function ($id) {
            $event = \App\Models\Event::with(['category', 'tickets'])->findOrFail($id);
            return view('dashboard-customer.show', compact('event'));
        });

        // Memproses ke halaman Checkout
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
            return view('dashboard-customer.checkout', compact('event', 'selectedTickets', 'totalPrice', 'totalTickets'));
        })->name('checkout.process');

        // Menampilkan Halaman QRIS Pembayaran
        Route::post('/payment', function (\Illuminate\Http\Request $request) {
            $eventId = $request->event_id;
            $tickets = $request->tickets;
            $totalPrice = $request->total_price;
            return view('dashboard-customer.payment', compact('eventId', 'tickets', 'totalPrice'));
        });

        // Memproses Pembayaran & Menghasilkan E-Ticket ke Database
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

            // 1. Simpan Transaksi dengan Reference Number
            $transaction = \App\Models\Transaction::create([
                'user_id' => auth()->id(),
                'total_price' => $totalPrice, 
                'status' => 'paid', 
                'reference_number' => 'TRX-' . strtoupper(Str::random(10)),
            ]);

            // 2. Simpan E-Ticket dengan Ticket Code
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
                        // 3. Kurangi Stok Tiket
                        $ticket = \App\Models\Ticket::find($ticketId);
                        if($ticket && $ticket->stock >= $quantity) {
                            $ticket->decrement('stock', $quantity);
                        }
                    }
                }
            }
            return redirect()->route('my-tickets')->with('success', 'Pembayaran berhasil! E-Ticket kamu sudah terbit.');
        });

        // Menampilkan Halaman E-Ticket Saya
        Route::get('/my-tickets', function () {
            $myTickets = \App\Models\Eticket::with(['ticket.event'])->where('user_id', auth()->id())->latest()->get();
            return view('dashboard-customer.my-tickets', compact('myTickets'));
        })->name('my-tickets');

        // Menampilkan Halaman Pesanan / Transaksi Saya
        Route::get('/my-orders', function () {
            $transactions = \App\Models\Transaction::where('user_id', auth()->id())
                            ->orderBy('created_at', 'desc')
                            ->get();
            return view('dashboard.my-orders', compact('transactions'));
        })->name('my-orders');
    });
});