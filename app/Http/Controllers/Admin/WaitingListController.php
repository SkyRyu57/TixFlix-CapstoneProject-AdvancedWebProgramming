<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaitingList;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WaitingListController extends Controller
{
    public function index(Request $request)
    {
        $query = WaitingList::with(['user', 'event']);
        
        // Filter by status
        if ($request->has('status') && in_array($request->status, ['waiting', 'invited', 'expired', 'completed'])) {
            $query->where('status', $request->status);
        }
        
        $waitingLists = $query->latest()->paginate(20);
        
        return view('admin.waiting-lists.index', compact('waitingLists'));
    }
    
    public function byEvent(Event $event)
    {
        $waitingLists = WaitingList::with('user')
            ->where('event_id', $event->id)
            ->orderBy('queue_number')
            ->get();
            
        return view('admin.waiting-lists.by-event', compact('event', 'waitingLists'));
    }
    
    public function invite(WaitingList $waiting, Request $request)
    {
        $request->validate([
            'expiry_hours' => 'required|integer|min:1|max:72'
        ]);
        
        $expiresAt = Carbon::now()->addHours($request->expiry_hours);
        
        $waiting->update([
            'status' => 'invited',
            'expires_at' => $expiresAt
        ]);
        
        // TODO: Send email notification to user
        
        return redirect()->back()->with('success', 'User invited to checkout. Expires at ' . $expiresAt->format('d M Y H:i'));
    }
    
    public function inviteNext(Event $event)
    {
        $nextWaiting = WaitingList::where('event_id', $event->id)
            ->where('status', 'waiting')
            ->orderBy('queue_number')
            ->first();
            
        if (!$nextWaiting) {
            return redirect()->back()->with('error', 'No waiting users in queue');
        }
        
        $expiresAt = Carbon::now()->addHours(24);
        
        $nextWaiting->update([
            'status' => 'invited',
            'expires_at' => $expiresAt
        ]);
        
        // TODO: Send email notification to user
        
        return redirect()->back()->with('success', 'Invited ' . $nextWaiting->user->name . ' to checkout. Expires at ' . $expiresAt->format('d M Y H:i'));
    }
}