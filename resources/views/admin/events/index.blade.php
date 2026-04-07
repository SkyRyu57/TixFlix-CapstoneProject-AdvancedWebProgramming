@extends('admin.layouts.master')

@section('title', 'All Events')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">All Events</h6>
            <a href="{{ route('admin.events.pending') }}" class="btn btn-sm btn-warning">Pending Events</a>
        </div>
        
        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead>
                    <tr class="text-white">
                        <th>ID</th>
                        <th>Title</th>
                        <th>Organizer</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr>
                        <td>{{ $event->id }}</td>
                        <td>{{ Str::limit($event->title, 40) }}</td>
                        <td>{{ $event->user->name ?? 'N/A' }}</td>
                        <td>{{ $event->category->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}</td>
                        <td>
                            @if($event->status == 'published')
                                <span class="badge bg-success text-white">Published</span>
                            @elseif($event->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($event->status == 'rejected')
                                <span class="badge bg-danger text-white">Rejected</span>
                            @else
                                <span class="badge bg-secondary">Cancelled</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.events.show', $event) }}" class="btn btn-sm btn-primary">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No events found</td>
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