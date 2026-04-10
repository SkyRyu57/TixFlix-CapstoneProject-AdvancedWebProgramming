<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Eticket;
use App\Models\Ticket;
use App\Models\Event;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $order_id = $request->query('order_id', 'TIX-' . time() . '-' . rand(100, 999));
        $payment_success = $request->query('payment_success', false);
        $payment_expired = $request->query('expired', false);
        
        $event_id = session('payment_event_id');
        $ticket_id = session('payment_ticket_id');
        $quantity = session('payment_quantity', 1);
        $total_amount = session('payment_total_price', 0);
        
        $event = Event::find($event_id);
        $ticket = Ticket::find($ticket_id);
        
        // Cek apakah transaksi sudah ada (pakai order_id)
        $transaction = Transaction::where('order_id', $order_id)->first();
        
        if ($payment_success == 'true') {
            return view('dashboard-customer.payment', compact('order_id', 'event', 'ticket', 'quantity', 'total_amount'))->with('payment_success', true);
        }
        
        if ($payment_expired == 'true') {
            return view('dashboard-customer.payment', compact('order_id', 'event', 'ticket', 'quantity', 'total_amount'))->with('payment_expired', true);
        }
        
        if (!$transaction && $event && $ticket) {
            Transaction::updateOrCreate(
                ['order_id' => $order_id],
                [
                    'user_id' => auth()->id(),
                    'ticket_id' => $ticket_id,
                    'event_id' => $event_id,
                    'total_price' => $total_amount,
                    'quantity' => $quantity,
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes(5),
                    'reference_number' => 'REF-' . strtoupper(Str::random(10)),
                ]
            );
        }
        
        return view('dashboard-customer.payment', compact('order_id', 'event', 'ticket', 'quantity', 'total_amount'));
    }
    
    public function mobile($order_id)
    {
        $transaction = Transaction::where('order_id', $order_id)->first();
        
        if (!$transaction) {
            return redirect()->route('dashboard')->with('error', 'Transaksi tidak ditemukan');
        }
        
        if ($transaction->expires_at && Carbon::now()->greaterThan($transaction->expires_at)) {
            return redirect()->route('payment.page', ['order_id' => $order_id, 'expired' => 'true']);
        }
        
        $event = Event::find($transaction->event_id);
        $remaining_seconds = $transaction->expires_at ? Carbon::now()->diffInSeconds($transaction->expires_at, false) : 300;
        
        if ($remaining_seconds <= 0) {
            return redirect()->route('payment.page', ['order_id' => $order_id, 'expired' => 'true']);
        }
        
        return view('dashboard-customer.payment-mobile', [
            'order_id' => $order_id,
            'event' => $event,
            'total_amount' => $transaction->total_price,
            'quantity' => $transaction->quantity,
            'ticket_id' => $transaction->ticket_id,
            'remaining_seconds' => $remaining_seconds
        ]);
    }
    
    public function process(Request $request)
    {
        try {
            $order_id = $request->order_id;
            $amount = $request->amount;
            $ticket_id = $request->ticket_id;
            $quantity = $request->quantity;
            
            $transaction = Transaction::where('order_id', $order_id)->first();
            
            if ($transaction && $transaction->status === 'success') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi sudah diproses sebelumnya'
                ]);
            }
            
            if ($transaction && $transaction->expires_at && Carbon::now()->greaterThan($transaction->expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu pembayaran telah habis'
                ]);
            }
            
            if (!$transaction) {
                $ticket = Ticket::find($ticket_id);
                
                $transaction = Transaction::create([
                    'order_id' => $order_id,
                    'user_id' => auth()->id(),
                    'ticket_id' => $ticket_id,
                    'event_id' => $ticket->event_id ?? null,
                    'total_price' => $amount,
                    'quantity' => $quantity,
                    'status' => 'success',
                    'expires_at' => Carbon::now()->addMinutes(5),
                    'paid_at' => Carbon::now(),
                    'reference_number' => 'REF-' . strtoupper(Str::random(10)),
                ]);
            } else {
                $transaction->update([
                    'status' => 'success',
                    'paid_at' => Carbon::now()
                ]);
            }
            
            $ticketModel = Ticket::find($ticket_id);
            $event = Event::find($ticketModel->event_id);
            $lastTicketCode = '';
            
            for ($i = 0; $i < $quantity; $i++) {
                $ticketCode = 'TIX-' . strtoupper(Str::random(8)) . '-' . rand(1000, 9999);
                $lastTicketCode = $ticketCode;
                
                Eticket::create([
                    'user_id' => auth()->id(),
                    'ticket_id' => $ticket_id,
                    'event_id' => $event->id,
                    'ticket_code' => $ticketCode,
                    'status' => 'active',
                    'purchase_date' => Carbon::now(),
                    'qr_data' => $ticketCode
                ]);
            }
            
            if ($ticketModel && $ticketModel->stock >= $quantity) {
                $ticketModel->decrement('stock', $quantity);
            }
            
            \App\Models\Notification::create([
                'user_id' => auth()->id(),
                'title' => 'Pembayaran Berhasil! 🎉',
                'message' => 'Pembayaran untuk ' . $quantity . ' tiket telah berhasil. Klik untuk lihat tiket Anda.',
                'type' => 'success',
                'link' => route('my-tickets'),
                'is_read' => false,
            ]);
            
            session()->forget(['payment_event_id', 'payment_ticket_id', 'payment_quantity', 'payment_total_price', 'payment_selected_tickets']);
            
            return response()->json([
                'success' => true,
                'ticket_code' => $lastTicketCode,
                'message' => 'Pembayaran berhasil'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function expired(Request $request)
    {
        $order_id = $request->order_id;
        
        $transaction = Transaction::where('order_id', $order_id)->first();
        
        if ($transaction && $transaction->status === 'pending') {
            $transaction->update([
                'status' => 'expired'
            ]);
        }
        
        return response()->json(['success' => true]);
    }
}