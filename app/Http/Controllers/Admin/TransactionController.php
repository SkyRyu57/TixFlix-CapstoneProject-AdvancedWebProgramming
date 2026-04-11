<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user');

        // Search by reference or customer name/email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_number', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'ilike', "%{$search}%")
                         ->orWhere('email', 'ilike', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['pending', 'paid', 'failed'])) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        if ($sort == 'latest') $query->orderBy('created_at', 'desc');
        elseif ($sort == 'oldest') $query->orderBy('created_at', 'asc');
        elseif ($sort == 'amount_high') $query->orderBy('total_price', 'desc');
        elseif ($sort == 'amount_low') $query->orderBy('total_price', 'asc');

        $transactions = $query->paginate(15);

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
        $oldStatus = $transaction->status;
        $transaction->status = $request->status;
        $transaction->save();

        // If status changed to paid, maybe we need to create etickets? But etickets already created at checkout time? In current flow, etickets are created when payment is processed (midtrans). For manual payment confirmation, we might need to generate etickets here. However, for simplicity, we just update status.
        // You can add notification to user here.

        return back()->with('success', "Status transaksi diubah dari {$oldStatus} menjadi {$request->status}.");
    }

    public function exportCsv(Request $request)
    {
        $query = Transaction::with('user');
        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_number', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'ilike', "%{$search}%")
                         ->orWhere('email', 'ilike', "%{$search}%");
                  });
            });
        }
        if ($request->filled('status') && in_array($request->status, ['pending', 'paid', 'failed'])) {
            $query->where('status', $request->status);
        }
        $transactions = $query->get();

        $filename = 'transactions_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');
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

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}