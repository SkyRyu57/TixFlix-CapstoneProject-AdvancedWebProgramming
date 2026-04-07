@extends('admin.layouts.master')

@section('title', 'All Waiting Lists')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">All Waiting Lists</h6>
            <div>
                <a href="{{ route('admin.waiting-lists.index') }}?status=waiting" class="btn btn-sm btn-warning me-1">Waiting</a>
                <a href="{{ route('admin.waiting-lists.index') }}?status=invited" class="btn btn-sm btn-info me-1">Invited</a>
                <a href="{{ route('admin.waiting-lists.index') }}?status=expired" class="btn btn-sm btn-danger me-1">Expired</a>
                <a href="{{ route('admin.waiting-lists.index') }}?status=completed" class="btn btn-sm btn-success me-1">Completed</a>
                <a href="{{ route('admin.waiting-lists.index') }}" class="btn btn-sm btn-secondary">All</a>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead>
                    <tr class="text-white">
                        <th>ID</th>
                        <th>Event</th>
                        <th>User</th>
                        <th>Queue Number</th>
                        <th>Status</th>
                        <th>Expires At</th>
                        <th>Joined At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waitingLists as $waiting)
                    <tr>
                        <td>{{ $waiting->id }}</td>
                        <td>
                            <a href="{{ route('admin.events.show', $waiting->event) }}" class="text-white">
                                {{ Str::limit($waiting->event->title ?? 'N/A', 40) }}
                            </a>
                        </td>
                        <td>{{ $waiting->user->name ?? 'N/A' }}<br>
                            <small class="text-muted">{{ $waiting->user->email ?? '' }}</small>
                        </td>
                        <td><span class="badge bg-secondary">#{{ $waiting->queue_number }}</span></td>
                        <td>
                            @if($waiting->status == 'waiting')
                                <span class="badge bg-warning">Waiting</span>
                            @elseif($waiting->status == 'invited')
                                <span class="badge bg-info">Invited</span>
                            @elseif($waiting->status == 'expired')
                                <span class="badge bg-danger">Expired</span>
                            @else
                                <span class="badge bg-success">Completed</span>
                            @endif
                        </td>
                        <td>
                            @if($waiting->expires_at)
                                {{ \Carbon\Carbon::parse($waiting->expires_at)->format('d M Y H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $waiting->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.waiting-lists.by-event', $waiting->event) }}" class="btn btn-sm btn-info">
                                View Event Queue
                            </a>
                            @if($waiting->status == 'waiting')
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#inviteModal{{ $waiting->id }}">
                                    Invite
                                </button>
                                
                                <!-- Invite Modal -->
                                <div class="modal fade" id="inviteModal{{ $waiting->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content bg-secondary">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Invite User to Checkout</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.waiting-lists.invite', $waiting) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <p>Invite <strong>{{ $waiting->user->name }}</strong> to checkout for event:</p>
                                                    <p class="text-primary">{{ $waiting->event->title }}</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Expiry Hours (for checkout)</label>
                                                        <input type="number" name="expiry_hours" class="form-control bg-dark text-white" value="24" min="1" max="72" required>
                                                        <small class="text-muted">User must complete checkout within this time</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Send Invitation</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No waiting lists found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $waitingLists->links() }}
        </div>
    </div>
</div>
@endsection