<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user');
        
        // Filter by status
        if ($request->has('status') && in_array($request->status, ['pending', 'paid', 'failed'])) {
            $query->where('status', $request->status);
        }
        
        $transactions = $query->latest()->paginate(20);
        
        return view('admin.transactions.index', compact('transactions'));
    }
    
    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'etickets.ticket.event']);
        return view('admin.transactions.show', compact('transaction'));
    }
    
    public function updateStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,failed'
        ]);
        
        $transaction->update(['status' => $request->status]);
        
        return redirect()->back()->with('success', 'Transaction status updated to ' . ucfirst($request->status));
    }
}