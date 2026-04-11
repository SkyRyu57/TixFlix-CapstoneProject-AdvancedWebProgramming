<?php
// app/Http/Controllers/Organizer/WaitingListController.php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\WaitingRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WaitingListController extends Controller
{
    public function index()
    {
        $organizerId = auth()->id();
        $events = Event::where('user_id', $organizerId)
            ->whereHas('waitingRequests', function ($q) {
                $q->where('status', 'pending');
            })
            ->withCount(['waitingRequests' => function ($q) {
                $q->where('status', 'pending');
            }])
            ->get();

        return view('organizer.waiting-requests.index', compact('events'));
    }

    public function show(Event $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403);
        }
        $requests = $event->waitingRequests()
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        return view('organizer.waiting-requests.show', compact('event', 'requests'));
    }

    public function inviteMultiple(Request $request, Event $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403);
        }
        $request->validate(['slots' => 'required|integer|min:1']);

        $pending = $event->waitingRequests()
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->take($request->slots)
            ->get();

        foreach ($pending as $req) {
            $req->update([
                'status' => 'invited',
                'expires_at' => Carbon::now()->addHours(24),
            ]);
            // TODO: Kirim notifikasi ke customer
        }

        return back()->with('success', $pending->count() . ' request diundang.');
    }

    public function cancel(WaitingRequest $request)
    {
        if ($request->event->user_id !== auth()->id()) {
            abort(403);
        }
        if ($request->status === 'invited') {
            $request->update(['status' => 'pending', 'expires_at' => null]);
            return back()->with('success', 'Undangan dibatalkan.');
        }
        return back()->with('error', 'Status tidak valid.');
    }
}