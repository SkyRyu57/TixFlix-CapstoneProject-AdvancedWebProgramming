<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\Ticket;
use App\Models\Eticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $organizerId = auth()->id();

        // Ambil semua event milik organizer
        $events = Event::where('user_id', $organizerId)->get();

        // 1. Total Revenue (paid)
        $totalRevenue = Transaction::where('status', 'paid')
            ->whereHas('etickets.ticket.event', function($q) use ($organizerId) {
                $q->where('user_id', $organizerId);
            })->sum('total_price');

        // 2. Total Events
        $totalEvents = $events->count();

        // 3. Rata-rata tiket terjual per event
        $totalSoldTickets = 0;
        foreach ($events as $event) {
            $totalSoldTickets += Eticket::whereHas('ticket', function($q) use ($event) {
                $q->where('event_id', $event->id);
            })->count();
        }
        $avgSoldPerEvent = $totalEvents > 0 ? round($totalSoldTickets / $totalEvents, 2) : 0;

        // 4. Chart data: jumlah tiket terjual per hari di bulan berjalan
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Generate semua tanggal dalam bulan ini
        $dateRange = [];
        $currentDate = $startOfMonth->copy();
        while ($currentDate->lte($endOfMonth)) {
            $dateRange[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        // Query jumlah tiket terjual per hari (berdasarkan created_at dari etickets)
        $dailyTicketSales = Eticket::whereHas('ticket.event', function($q) use ($organizerId) {
                $q->where('user_id', $organizerId);
            })
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->pluck('total', 'date');

        $chartValues = [];
        foreach ($dateRange as $d) {
            $chartValues[] = $dailyTicketSales->get($d, 0);
        }

        // Format tanggal untuk tampilan (hanya tanggal)
        $chartLabels = array_map(function($date) {
            return date('d', strtotime($date));
        }, $dateRange);
        $chartLabelsFull = $dateRange; 

        return view('organizer.dashboard', compact(
            'totalRevenue',
            'totalEvents',
            'avgSoldPerEvent',
            'chartLabels',
            'chartValues',
            'chartLabelsFull'
        ));
    }
}