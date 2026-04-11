<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total stats
        $totalEvents = Event::count();
        $totalTicketsSold = DB::table('etickets')->count();
        $totalRevenue = Transaction::where('status', 'paid')->sum('total_price');
        $totalOrganizers = User::where('role', 'organizer')->count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Chart data: revenue per month (last 6 months)
        $monthLabels = [];
        $revenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthLabels[] = $month->format('M Y');
            $revenue = Transaction::where('status', 'paid')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_price');
            $revenueData[] = $revenue;
        }

        // Pie chart: revenue per event (top 5 events)
        $eventRevenueData = Transaction::where('transactions.status', 'paid')
            ->join('etickets', 'transactions.id', '=', 'etickets.transaction_id')
            ->join('tickets', 'etickets.ticket_id', '=', 'tickets.id')
            ->join('events', 'tickets.event_id', '=', 'events.id')
            ->select('events.title', DB::raw('SUM(transactions.total_price) as total'))
            ->groupBy('events.id', 'events.title')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->pluck('total', 'title')
            ->toArray();
        // Recent notifications (for admin)
        $recentNotifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent organizers
        $recentOrganizers = User::where('role', 'organizer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent events
        $recentEvents = Event::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalEvents', 'totalTicketsSold', 'totalRevenue', 'totalOrganizers', 'totalCustomers',
            'monthLabels', 'revenueData', 'eventRevenueData', 'recentNotifications',
            'recentOrganizers', 'recentEvents'
        ));
    }
}