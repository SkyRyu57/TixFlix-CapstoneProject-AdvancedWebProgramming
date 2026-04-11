<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['user', 'category']);

        // Search by title (case-insensitive, contains)
        if ($request->filled('search')) {
            $query->where('title', 'ilike', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'start_soon':
                $query->orderBy('start_date', 'asc');
                break;
            case 'start_later':
                $query->orderBy('start_date', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $events = $query->paginate(10);

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $organizers = User::where('role', 'organizer')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('admin.events.create', compact('organizers', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id|in:' . User::where('role', 'organizer')->pluck('id')->implode(','),
            'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:pending,published,rejected,cancelled',
            'tickets' => 'required|array|min:1',
            'tickets.*.name' => 'required|string|max:255',
            'tickets.*.price' => 'required|integer|min:0',
            'tickets.*.stock' => 'required|integer|min:0',
            'tickets.*.description' => 'nullable|string',
        ]);

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('events', 'public');
        }

        $event = Event::create([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'description' => $request->description,
            'location' => $request->location,
            'banner' => $bannerPath,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse($request->end_date),
            'status' => $request->status,
            'approved_by' => auth()->id(),
            'approved_at' => $request->status == 'published' ? now() : null,
        ]);

        foreach ($request->tickets as $ticketData) {
            Ticket::create([
                'event_id' => $event->id,
                'name' => $ticketData['name'],
                'price' => $ticketData['price'],
                'stock' => $ticketData['stock'],
                'description' => $ticketData['description'] ?? null,
            ]);
        }

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dibuat.');
    }

    public function edit(Event $event)
    {
        $organizers = User::where('role', 'organizer')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('admin.events.edit', compact('event', 'organizers', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id|in:' . User::where('role', 'organizer')->pluck('id')->implode(','),
            'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:pending,published,rejected,cancelled',
            'tickets' => 'required|array|min:1',
            'tickets.*.name' => 'required|string|max:255',
            'tickets.*.price' => 'required|integer|min:0',
            'tickets.*.stock' => 'required|integer|min:0',
            'tickets.*.description' => 'nullable|string',
            'tickets.*.id' => 'nullable|exists:tickets,id',
        ]);

        $bannerPath = $event->banner;
        if ($request->hasFile('banner')) {
            if ($bannerPath && \Storage::disk('public')->exists($bannerPath)) {
                \Storage::disk('public')->delete($bannerPath);
            }
            $bannerPath = $request->file('banner')->store('events', 'public');
        }

        $event->update([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'description' => $request->description,
            'location' => $request->location,
            'banner' => $bannerPath,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse($request->end_date),
            'status' => $request->status,
            'approved_by' => auth()->id(),
            'approved_at' => $request->status == 'published' ? now() : null,
        ]);

        // Update existing tickets and create new ones
        $existingTicketIds = $event->tickets->pluck('id')->toArray();
        $submittedTicketIds = [];
        foreach ($request->tickets as $ticketData) {
            if (isset($ticketData['id']) && in_array($ticketData['id'], $existingTicketIds)) {
                $ticket = Ticket::find($ticketData['id']);
                $ticket->update([
                    'name' => $ticketData['name'],
                    'price' => $ticketData['price'],
                    'stock' => $ticketData['stock'],
                    'description' => $ticketData['description'] ?? null,
                ]);
                $submittedTicketIds[] = $ticket->id;
            } else {
                $ticket = Ticket::create([
                    'event_id' => $event->id,
                    'name' => $ticketData['name'],
                    'price' => $ticketData['price'],
                    'stock' => $ticketData['stock'],
                    'description' => $ticketData['description'] ?? null,
                ]);
                $submittedTicketIds[] = $ticket->id;
            }
        }

        // Delete tickets that were removed
        $toDelete = array_diff($existingTicketIds, $submittedTicketIds);
        Ticket::whereIn('id', $toDelete)->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        // Check if event has any transaction
        $hasTransactions = $event->tickets()->whereHas('etickets')->exists();
        if ($hasTransactions) {
            return back()->with('error', 'Event tidak bisa dihapus karena sudah memiliki transaksi.');
        }

        // Delete banner
        if ($event->banner && \Storage::disk('public')->exists($event->banner)) {
            \Storage::disk('public')->delete($event->banner);
        }

        // Delete tickets (cascade should handle but explicit)
        $event->tickets()->delete();
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus.');
    }

    public function approve(Event $event)
    {
        if ($event->status !== 'pending') {
            return back()->with('error', 'Event tidak dalam status pending.');
        }
        $event->update([
            'status' => 'published',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Event berhasil disetujui.');
    }

    public function reject(Event $event)
    {
        if ($event->status !== 'pending') {
            return back()->with('error', 'Event tidak dalam status pending.');
        }
        $event->update(['status' => 'rejected']);
        return back()->with('success', 'Event ditolak.');
    }
    public function exportCsv()
    {
        $events = Event::with(['user', 'category'])->get();
        $filename = 'events_' . date('Y-m-d') . '.csv';
        
        $handle = fopen('php://temp', 'w');
        fputcsv($handle, ['ID', 'Title', 'Organizer', 'Category', 'Start Date', 'Status', 'Revenue']);
        foreach ($events as $event) {
            $revenue = Transaction::whereHas('etickets.ticket', function($q) use ($event) {
                $q->where('event_id', $event->id);
            })->where('status', 'paid')->sum('total_price');
            fputcsv($handle, [
                $event->id,
                $event->title,
                $event->user->name ?? '-',
                $event->category->name ?? '-',
                $event->start_date->format('Y-m-d'),
                $event->status,
                $revenue,
            ]);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    public function printView(Request $request)
    {
        $query = Event::with(['user', 'category']);
        
        if ($request->filled('search')) {
            $query->where('title', 'ilike', '%' . $request->search . '%');
        }
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        $events = $query->get();
        
        return view('admin.events.print', compact('events'));
    }
}