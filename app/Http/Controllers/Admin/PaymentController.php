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
        // Ambil transaksi dengan status pending dan memiliki relasi payment (dengan proof_image)
        $transactions = Transaction::with(['user', 'payment'])
            ->where('status', 'pending')
            ->whereHas('payment', function($q) {
                $q->whereNotNull('proof_image');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.payments.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'payment', 'etickets.ticket']);
        return view('admin.payments.show', compact('transaction'));
    }

    public function approve(Transaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah diproses sebelumnya.');
        }

        $transaction->status = 'paid';
        $transaction->paid_at = now();
        $transaction->save();

        // Update status payment jika ada
        if ($transaction->payment) {
            $transaction->payment->update([
                'status' => 'verified',
                'verified_at' => now(),
            ]);
        }

        // Update e-tickets: set issued_at jika belum
        foreach ($transaction->etickets as $eticket) {
            if (!$eticket->issued_at) {
                $eticket->issued_at = now();
                $eticket->save();
            }
        }

        // Kurangi stok tiket (karena saat upload bukti stok belum dikurangi)
        foreach ($transaction->etickets as $eticket) {
            $ticket = $eticket->ticket;
            if ($ticket && $ticket->stock > 0) {
                $ticket->decrement('stock');
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
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah diproses sebelumnya.');
        }

        $transaction->status = 'failed';
        $transaction->save();

        // Update status payment jika ada
        if ($transaction->payment) {
            $transaction->payment->update(['status' => 'rejected']);
        }

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