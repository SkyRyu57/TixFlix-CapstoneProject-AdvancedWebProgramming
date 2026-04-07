<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventController extends Controller
{
    public function pending()
    {
        $events = Event::where('status', 'pending')
            ->with(['user', 'category'])
            ->latest()
            ->paginate(20);
        return view('admin.events.pending', compact('events'));
    }
    
    public function approve(Event $event)
    {
        $event->update([
            'status' => 'published',
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now()
        ]);
        
        return redirect()->back()->with('success', 'Event approved successfully');
    }
    
    public function reject(Event $event)
    {
        $event->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Event rejected');
    }
    
    public function index()
    {
        $events = Event::with(['user', 'category'])->latest()->paginate(20);
        return view('admin.events.index', compact('events'));
    }
    
    public function show(Event $event)
    {
        $event->load(['user', 'category', 'tickets']);
        return view('admin.events.show', compact('event'));
    }
}