<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\Eticket;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Statistik Utama
        $totalEvents = Event::count();
        $totalTicketsSold = Eticket::count();
        $totalRevenue = Transaction::where('status', 'paid')->sum('total_price');
        $totalOrganizers = User::where('role', 'organizer')->count();
        $totalUsers = User::count();
        $totalTransactions = Transaction::count();
        
        // Data untuk Chart Pendapatan (6 bulan terakhir)
        $monthLabels = [];
        $revenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthLabels[] = $month->translatedFormat('F Y');
            $revenue = Transaction::where('status', 'paid')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_price');
            $revenueData[] = (float) $revenue;
        }
        
        // Data untuk Chart Lingkaran (Pendapatan per Event Top 5)
        $eventRevenue = DB::table('events')
            ->leftJoin('tickets', 'events.id', '=', 'tickets.event_id')
            ->leftJoin('etickets', 'tickets.id', '=', 'etickets.ticket_id')
            ->leftJoin('transactions', 'etickets.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'paid')
            ->select('events.title', DB::raw('SUM(tickets.price) as total_revenue'))
            ->groupBy('events.id', 'events.title')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();
        
        $eventRevenueData = [];
        foreach ($eventRevenue as $item) {
            $eventRevenueData[$item->title] = (float) $item->total_revenue;
        }
        
        // Notifikasi Terbaru
        $recentNotifications = Notification::latest()->limit(5)->get();
        
        // Penyelenggara Terbaru
        $recentOrganizers = User::where('role', 'organizer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Event Terbaru
        $recentEvents = Event::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Top Events (untuk tabel)
        $topEvents = DB::table('events')
            ->leftJoin('tickets', 'events.id', '=', 'tickets.event_id')
            ->leftJoin('etickets', 'tickets.id', '=', 'etickets.ticket_id')
            ->select('events.id', 'events.title', DB::raw('COUNT(etickets.id) as tickets_sold'))
            ->groupBy('events.id', 'events.title')
            ->orderBy('tickets_sold', 'desc')
            ->limit(5)
            ->get();
        
        $topEventsLabels = $topEvents->pluck('title')->map(function($title) {
            return \Illuminate\Support\Str::limit($title, 20);
        })->toArray();
        $topEventsData = $topEvents->pluck('tickets_sold')->toArray();
        
        return view('admin.dashboard', compact(
            'totalEvents',
            'totalTicketsSold',
            'totalRevenue',
            'totalOrganizers',
            'totalUsers',
            'totalTransactions',
            'monthLabels',
            'revenueData',
            'eventRevenueData',
            'recentNotifications',
            'recentOrganizers',
            'recentEvents',
            'topEventsLabels',
            'topEventsData'
        ));
    }
}