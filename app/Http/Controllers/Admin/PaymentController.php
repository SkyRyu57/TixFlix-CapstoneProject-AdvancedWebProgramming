<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('user')
            ->where('status', 'pending')
            ->whereNotNull('payment_proof')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.payments.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        return view('admin.payments.show', compact('transaction'));
    }

    public function approve(Transaction $transaction)
    {
        $transaction->status = 'paid';
        $transaction->paid_at = now();
        $transaction->save();

        // Update e-tickets: set issued_at jika belum
        foreach ($transaction->etickets as $eticket) {
            if (!$eticket->issued_at) {
                $eticket->issued_at = now();
                $eticket->save();
            }
        }

        // Notifikasi ke customer
        Notification::create([
            'user_id' => $transaction->user_id,
            'title' => 'Pembayaran Dikonfirmasi',
            'message' => 'Pembayaran Anda telah dikonfirmasi. Tiket Anda sudah dapat diunduh.',
            'type' => 'success',
            'link' => route('my-tickets'),
            'is_read' => false,
        ]);

        return redirect()->route('admin.payments.index')->with('success', 'Pembayaran disetujui dan e-ticket diterbitkan.');
    }

    public function reject(Transaction $transaction)
    {
        $transaction->status = 'failed';
        $transaction->save();

        Notification::create([
            'user_id' => $transaction->user_id,
            'title' => 'Pembayaran Ditolak',
            'message' => 'Pembayaran Anda ditolak. Silakan hubungi admin.',
            'type' => 'error',
            'link' => route('dashboard'),
            'is_read' => false,
        ]);

        return redirect()->route('admin.payments.index')->with('success', 'Pembayaran ditolak.');
    }
}