@extends('admin.layouts.master')

@section('title', $event->title)

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Event Detail: {{ $event->title }}</h6>
            <div>
                <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-primary">Back to Events</a>
                @if($event->status == 'pending')
                    <form action="{{ route('admin.events.approve', $event) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                    </form>
                    <form action="{{ route('admin.events.reject', $event) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                    </form>
                @endif
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                @if($event->banner)
                    <img src="{{ asset('storage/' . $event->banner) }}" class="img-fluid rounded" alt="{{ $event->title }}">
                @else
                    <div class="bg-dark text-center p-5 rounded">
                        <i class="fa fa-image fa-5x text-muted"></i>
                        <p class="mt-2">No banner</p>
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr><th width="30%">Title</th><td>{{ $event->title }}</td></tr>
                    <tr><th>Slug</th><td>{{ $event->slug }}</td></tr>
                    <tr><th>Organizer</th><td>{{ $event->user->name ?? 'N/A' }} ({{ $event->user->email ?? 'N/A' }})</td></tr>
                    <tr><th>Category</th><td>{{ $event->category->name ?? 'N/A' }}</td></tr>
                    <tr><th>Location</th><td>{{ $event->location }}</td></tr>
                    <tr><th>Start Date</th><td>{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y H:i') }}</td></tr>
                    <tr><th>End Date</th><td>{{ \Carbon\Carbon::parse($event->end_date)->format('d M Y H:i') }}</td></tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($event->status == 'published')
                                <span class="badge bg-success">Published</span>
                            @elseif($event->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($event->status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-secondary">Cancelled</span>
                            @endif
                        </td>
                    </tr>
                    @if($event->approved_by)
                    <tr><th>Approved By</th><td>{{ $event->approvedBy->name ?? 'N/A' }} at {{ \Carbon\Carbon::parse($event->approved_at)->format('d M Y H:i') }}</td></tr>
                    @endif
                    <tr><th>Description</th><td>{{ nl2br($event->description) }}</td></tr>
                </table>
            </div>
        </div>
        
        <!-- Ticket Types -->
        <div class="mt-4">
            <h6 class="mb-3">Ticket Types</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Name</th><th>Price</th><th>Stock</th><th>Description</th></tr>
                    </thead>
                    <tbody>
                        @forelse($event->tickets as $ticket)
                        <tr><td>{{ $ticket->name }}</td>
                            <td>Rp {{ number_format($ticket->price, 0, ',', '.') }}</td>
                            <td>{{ number_format($ticket->stock) }}</td>
                            <td>{{ $ticket->description ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center">No tickets available</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection