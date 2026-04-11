<?php
namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Eticket;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index()
    {
        return view('organizer.scan.index');
    }

    public function scan(Request $request)
    {
        $ticketCode = $request->input('ticket_code');
        $ticket = Eticket::where('ticket_code', $ticketCode)
            ->whereHas('ticket.event', function($q) {
                $q->where('user_id', auth()->id());
            })->first();

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => '❌ Tiket tidak valid']);
        }
        if ($ticket->is_scanned) {
            return response()->json(['success' => false, 'message' => '⚠️ Tiket sudah digunakan']);
        }
        if (!$ticket->issued_at) {
            return response()->json(['success' => false, 'message' => '⏳ Tiket belum diterbitkan']);
        }
        $ticket->update(['is_scanned' => true, 'scanned_at' => now()]);
        return response()->json(['success' => true, 'message' => '✅ Tiket valid! Selamat datang']);
    }
}