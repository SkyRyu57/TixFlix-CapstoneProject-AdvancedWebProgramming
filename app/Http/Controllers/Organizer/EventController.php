<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Eticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     */
    public function index()
    {
        $events = Event::with(['category', 'tickets'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Statistik
        $totalEvents = Event::where('user_id', auth()->id())->count();
        $totalTicketsSold = Eticket::whereHas('ticket.event', function($query) {
            $query->where('user_id', auth()->id());
        })->count();
        
        $totalRevenue = Eticket::whereHas('ticket.event', function($query) {
            $query->where('user_id', auth()->id());
        })->join('tickets', 'etickets.ticket_id', '=', 'tickets.id')
          ->sum('tickets.price');
        
        $upcomingEvents = Event::where('user_id', auth()->id())
            ->where('status', 'published')
            ->where('start_date', '>=', now())
            ->count();
        
        return view('dashboard-organizer.events.index', compact(
            'events', 'totalEvents', 'totalTicketsSold', 'totalRevenue', 'upcomingEvents'
        ));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        $categories = Category::all();
        return view('dashboard-organizer.events.create', compact('categories'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
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

        // Handle banner upload
        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('events', 'public');
        }

        // Generate slug
        $slug = Str::slug($request->title) . '-' . Str::random(6);

        $event = Event::create([
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

        return redirect()->route('organizer.events.tickets', $event->id)
            ->with('success', 'Event berhasil dibuat! Sekarang tambahkan tiket untuk event ini.');
    }

    /**
     * Display the specified event.
     */
    public function show($id)
    {
        $event = Event::with(['category', 'tickets'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);
        
        // Statistik tiket terjual
        $ticketsSold = Eticket::whereHas('ticket', function($query) use ($id) {
            $query->where('event_id', $id);
        })->count();
        
        $revenue = Eticket::whereHas('ticket', function($query) use ($id) {
            $query->where('event_id', $id);
        })->join('tickets', 'etickets.ticket_id', '=', 'tickets.id')
          ->sum('tickets.price');
        
        $tickets = Ticket::where('event_id', $id)->get();
        
        return view('dashboard-organizer.events.show', compact('event', 'ticketsSold', 'revenue', 'tickets'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit($id)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($id);
        $categories = Category::all();
        return view('dashboard-organizer.events.edit', compact('event', 'categories'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, $id)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($id);
        
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

        // Handle banner upload
        if ($request->hasFile('banner')) {
            // Delete old banner
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

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event berhasil diperbarui!');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy($id)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($id);
        
        // Check if event has tickets sold
        $ticketsSold = Eticket::whereHas('ticket', function($query) use ($id) {
            $query->where('event_id', $id);
        })->count();
        
        if ($ticketsSold > 0) {
            return back()->with('error', 'Tidak dapat menghapus event yang sudah memiliki tiket terjual!');
        }
        
        // Delete banner
        if ($event->banner && Storage::disk('public')->exists($event->banner)) {
            Storage::disk('public')->delete($event->banner);
        }
        
        // Delete tickets
        Ticket::where('event_id', $id)->delete();
        
        $event->delete();
        
        return redirect()->route('organizer.events.index')
            ->with('success', 'Event berhasil dihapus!');
    }

    /**
     * Manage tickets for event.
     */
    public function tickets($id)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($id);
        $tickets = Ticket::where('event_id', $id)->get();
        
        return view('dashboard-organizer.events.tickets', compact('event', 'tickets'));
    }
}