@extends('admin.layouts.master')

@section('title', 'Pending Events')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Pending Events (Need Approval)</h6>
            <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-primary">All Events</a>
        </div>
        
        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead>
                    <tr class="text-white">
                        <th>ID</th>
                        <th>Title</th>
                        <th>Organizer</th>
                        <th>Location</th>
                        <th>Start Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr>
                        <td>{{ $event->id }}</td>
                        <td>{{ Str::limit($event->title, 50) }}</td>
                        <td>{{ $event->user->name ?? 'N/A' }}</td>
                        <td>{{ Str::limit($event->location, 30) }}</td>
                        <td>{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.events.show', $event) }}" class="btn btn-sm btn-info">Detail</a>
                            <form action="{{ route('admin.events.approve', $event) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this event?')">Approve</button>
                            </form>
                            <form action="{{ route('admin.events.reject', $event) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this event?')">Reject</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No pending events</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $events->links() }}
        </div>
    </div>
</div>
@endsection