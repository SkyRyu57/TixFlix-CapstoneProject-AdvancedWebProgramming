<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Today's data
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        
        // Today Sale (jumlah transaksi yang paid hari ini)
        $todaySale = Transaction::where('status', 'paid')
            ->whereDate('updated_at', $today)
            ->count();
        
        // Today Revenue (total pendapatan hari ini)
        $todayRevenue = Transaction::where('status', 'paid')
            ->whereDate('updated_at', $today)
            ->sum('total_price');
        
        // Total Sale (semua transaksi yang pernah paid)
        $totalSale = Transaction::where('status', 'paid')->count();
        
        // Total Revenue (semua pendapatan)
        $totalRevenue = Transaction::where('status', 'paid')->sum('total_price');
        
        // Recent Transactions (5 terakhir)
        $recentTransactions = Transaction::with(['user', 'etickets.ticket.event'])
            ->latest()
            ->take(5)
            ->get();
        
        // Events pending approval
        $pendingEvents = Event::where('status', 'pending')
            ->with(['user', 'category'])
            ->latest()
            ->take(5)
            ->get();
        
        // Chart data: Last 7 days sales & revenue
        $chartLabels = [];
        $chartSales = [];
        $chartRevenues = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M');
            
            $chartSales[] = Transaction::where('status', 'paid')
                ->whereDate('updated_at', $date)
                ->count();
            
            $chartRevenues[] = Transaction::where('status', 'paid')
                ->whereDate('updated_at', $date)
                ->sum('total_price');
        }
        
        return view('admin.dashboard', compact(
            'todaySale',
            'todayRevenue',
            'totalSale',
            'totalRevenue',
            'recentTransactions',
            'pendingEvents',
            'chartLabels',
            'chartSales',
            'chartRevenues'
        ));
    }
}