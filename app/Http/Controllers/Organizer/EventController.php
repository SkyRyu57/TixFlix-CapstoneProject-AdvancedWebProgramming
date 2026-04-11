<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $organizerId = auth()->id();

        $query = Event::where('user_id', $organizerId)->with(['category', 'tickets']);

        // Search (partial match)
        if ($request->filled('search')) {
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($request->search) . '%']);
        }

        // Filter status
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'start_soon':
                $query->orderBy('start_date', 'asc');
                break;
            case 'start_later':
                $query->orderBy('start_date', 'desc');
                break;
        }

        $events = $query->paginate(12);

        // Jika request dari AJAX (fetch), kembalikan hanya partial view
        if ($request->ajax()) {
            return view('organizer.events.partials.events_list', compact('events'));
        }

        // Jika request biasa (browser), kembalikan halaman lengkap dengan layout
        return view('organizer.events.index', compact('events'));
    }

    public function print(Request $request)
    {
        $eventIds = json_decode($request->input('event_ids', '[]'));
        if (empty($eventIds)) {
            return back()->with('error', 'Pilih minimal satu event.');
        }

        $events = Event::whereIn('id', $eventIds)
            ->where('user_id', auth()->id())
            ->with(['category', 'tickets'])
            ->get();

        return view('organizer.events.print', compact('events'));
    }
}