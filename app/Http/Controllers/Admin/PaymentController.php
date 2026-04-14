<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        // Perbaikan: ambil dari tabel payments, bukan transactions
        $payments = Payment::with(['user', 'transaction'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.payments.index', compact('payments'));
    }

    public function show(Transaction $transaction)
    {
        $payment = Payment::where('transaction_id', $transaction->id)->firstOrFail();
        return view('admin.payments.show', compact('transaction', 'payment'));
    }

    public function approve(Transaction $transaction)
    {
        DB::beginTransaction();
        try {
            // Update transaction
            $transaction->update([
                'status' => 'paid',
                'paid_at' => now()
            ]);
            
            // Update payment
            $payment = Payment::where('transaction_id', $transaction->id)->first();
            if ($payment) {
                $payment->update([
                    'status' => 'verified',
                    'verified_at' => now()
                ]);
            }
            
            // Update stock tickets
            foreach ($transaction->etickets as $eticket) {
                $ticket = \App\Models\Ticket::find($eticket->ticket_id);
                if ($ticket) {
                    $ticket->decrement('stock');
                }
            }
            
            // Send notification to customer
            Notification::create([
                'user_id' => $transaction->user_id,
                'title' => 'Pembayaran Diverifikasi! ✅',
                'message' => 'Pembayaran Anda telah diverifikasi. Tiket Anda sudah aktif!',
                'type' => 'success',
                'link' => route('my-tickets'),
                'is_read' => false,
            ]);
            
            DB::commit();
            return redirect()->route('admin.payments.index')->with('success', 'Pembayaran berhasil diverifikasi!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Transaction $transaction)
    {
        DB::beginTransaction();
        try {
            // Update transaction
            $transaction->update(['status' => 'failed']);
            
            // Update payment
            $payment = Payment::where('transaction_id', $transaction->id)->first();
            if ($payment) {
                $payment->update(['status' => 'rejected']);
            }
            
            // Send notification to customer
            Notification::create([
                'user_id' => $transaction->user_id,
                'title' => 'Pembayaran Ditolak! ❌',
                'message' => 'Moh maaf, pembayaran Anda ditolak. Silakan upload ulang bukti pembayaran yang valid.',
                'type' => 'error',
                'link' => route('payment.page'),
                'is_read' => false,
            ]);
            
            DB::commit();
            return redirect()->route('admin.payments.index')->with('success', 'Pembayaran ditolak!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}