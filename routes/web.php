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
        
        // Dashboard utama
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
            
            // Data untuk chart dan financial report
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
                
                // Detail tiket per event untuk laporan
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
        
        // Events list
        Route::get('/organizer/events', function () {
            $events = \App\Models\Event::with(['category'])
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            return view('dashboard-organizer.events', compact('events'));
        })->name('organizer.events');
        
        // Create event form
        Route::get('/organizer/events/create', function () {
            $categories = \App\Models\Category::all();
            return view('dashboard-organizer.event-create', compact('categories'));
        })->name('organizer.event.create');
        
        // Store event
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
        
        // Edit event form
        Route::get('/organizer/events/{id}/edit', function ($id) {
            $event = \App\Models\Event::where('user_id', auth()->id())->findOrFail($id);
            $categories = \App\Models\Category::all();
            return view('dashboard-organizer.event-edit', compact('event', 'categories'));
        })->name('organizer.event.edit');
        
        // Update event
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
        
        // Event detail
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
        
        // Manage tickets for event
        Route::get('/organizer/events/{id}/tickets', function ($id) {
            $event = \App\Models\Event::where('user_id', auth()->id())->findOrFail($id);
            $tickets = \App\Models\Ticket::where('event_id', $id)->get();
            
            return view('dashboard-organizer.tickets', compact('event', 'tickets'));
        })->name('organizer.event.tickets');
        
        // Store ticket
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
        
        // Update ticket
        Route::put('/organizer/events/{eventId}/tickets/{ticketId}', function (\Illuminate\Http\Request $request, $eventId, $ticketId) {
            $event = \App\Models\Event::where('user_id', auth()->id())->findOrFail($eventId);
            $ticket = \App\Models\Ticket::where('event_id', $eventId)->findOrFail($ticketId);
            
            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|integer|min:0',
                'stock' => 'required|integer|min:0',
                'description' => 'nullable|string',
            ]);

            $ticket->update([
                'name' => $request->name,
                'price' => $request->price,
                'stock' => $request->stock,
                'description' => $request->description,
            ]);

            return back()->with('success', 'Ticket updated!');
        })->name('organizer.ticket.update');
        
        // Delete ticket
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
        
        // Delete event
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
        
        // Attendees list
        Route::get('/organizer/attendees', function () {
            $attendees = \App\Models\Eticket::with(['ticket.event', 'user'])
                ->whereHas('ticket.event', function($q) {
                    $q->where('user_id', auth()->id());
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            
            return view('dashboard-organizer.attendees', compact('attendees'));
        })->name('organizer.attendees');
        
        // ========================================
        // EXPORT REPORT PDF (TANPA DOMPDF - MENGGUNAKAN BROWSER PRINT)
        // ========================================
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
                
                // Detail tiket per event
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
            
            // Tampilkan halaman laporan yang bisa di-print (tanpa DomPDF)
            return view('dashboard-organizer.report-pdf', $data);
        })->name('organizer.report.export.pdf');
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

        Route::post('/payment', function (\Illuminate\Http\Request $request) {
            $eventId = $request->event_id;
            $tickets = $request->tickets;
            $totalPrice = $request->total_price;
            return view('dashboard-customer.payment', compact('eventId', 'tickets', 'totalPrice'));
        })->name('payment.page');

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

            $transaction = \App\Models\Transaction::create([
                'user_id' => auth()->id(),
                'total_price' => $totalPrice,
                'status' => 'paid',
                'reference_number' => 'TRX-' . strtoupper(Str::random(10)),
            ]);

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

        Route::get('/my-tickets', function () {
            $myTickets = \App\Models\Eticket::with(['ticket.event', 'transaction'])
                ->where('user_id', auth()->id())
                ->latest()
                ->get();
            return view('dashboard-customer.my-tickets', compact('myTickets'));
        })->name('my-tickets');
        
        // ========================================
        // PRINT E-TICKET
        // ========================================
        Route::get('/ticket/{code}/print', function ($code) {
            $eticket = \App\Models\Eticket::with(['ticket.event', 'transaction'])
                ->where('ticket_code', $code)
                ->where('user_id', auth()->id())
                ->firstOrFail();
            
            return view('dashboard-customer.ticket-print', compact('eticket'));
        })->name('ticket.print');
    });
});