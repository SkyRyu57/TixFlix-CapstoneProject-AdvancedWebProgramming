<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Event;
use App\Models\Eticket;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function transactions(Request $request)
    {
        $organizerId = auth()->id();
        $eventId = $request->get('event_id');

        $query = Transaction::where('status', 'paid')
            ->whereHas('etickets.ticket.event', function($q) use ($organizerId) {
                $q->where('user_id', $organizerId);
            });

        if ($eventId) {
            $query->whereHas('etickets.ticket', function($q) use ($eventId) {
                $q->where('event_id', $eventId);
            });
        }

        $transactions = $query->with('user')->get();

        $filename = 'transactions_' . ($eventId ? 'event_' . $eventId : 'all') . '_' . Carbon::now()->format('Ymd_His') . '.csv';

        return response()->stream(
            function() use ($transactions) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['ID', 'Reference', 'Customer', 'Email', 'Total Price', 'Status', 'Date']);
                foreach ($transactions as $t) {
                    fputcsv($handle, [
                        $t->id,
                        $t->reference_number,
                        $t->user->name ?? 'N/A',
                        $t->user->email ?? 'N/A',
                        $t->total_price,
                        $t->status,
                        $t->created_at->format('Y-m-d H:i:s')
                    ]);
                }
                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    public function event(Event $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403);
        }

        $etickets = Eticket::whereHas('ticket', function($q) use ($event) {
            $q->where('event_id', $event->id);
        })->with('user', 'ticket')->get();

        $filename = 'event_' . $event->id . '_' . Carbon::now()->format('Ymd_His') . '.csv';

        return response()->stream(
            function() use ($etickets) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Ticket Code', 'Buyer Name', 'Email', 'Ticket Type', 'Price', 'Scanned', 'Scanned At']);
                foreach ($etickets as $e) {
                    fputcsv($handle, [
                        $e->ticket_code,
                        $e->user->name ?? 'N/A',
                        $e->user->email ?? 'N/A',
                        $e->ticket->name ?? 'N/A',
                        $e->ticket->price ?? 0,
                        $e->is_scanned ? 'Yes' : 'No',
                        $e->scanned_at ?? ''
                    ]);
                }
                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
}