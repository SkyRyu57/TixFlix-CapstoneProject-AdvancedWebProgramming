<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Store a newly created ticket.
     */
    public function store(Request $request, $eventId)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($eventId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        Ticket::create([
            'event_id' => $eventId,
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Tiket berhasil ditambahkan!');
    }

    /**
     * Update the specified ticket.
     */
    public function update(Request $request, $eventId, $ticketId)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($eventId);
        $ticket = Ticket::where('event_id', $eventId)->findOrFail($ticketId);
        
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

        return back()->with('success', 'Tiket berhasil diperbarui!');
    }

    /**
     * Remove the specified ticket.
     */
    public function destroy($eventId, $ticketId)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($eventId);
        $ticket = Ticket::where('event_id', $eventId)->findOrFail($ticketId);
        
        // Check if ticket has been sold
        $soldCount = \App\Models\Eticket::where('ticket_id', $ticketId)->count();
        
        if ($soldCount > 0) {
            return back()->with('error', 'Tidak dapat menghapus tiket yang sudah terjual!');
        }
        
        $ticket->delete();
        
        return back()->with('success', 'Tiket berhasil dihapus!');
    }
}