@extends('admin.layouts.master')

@section('title', 'Waiting List - ' . $event->title)

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Waiting List: {{ $event->title }}</h6>
            <div>
                <a href="{{ route('admin.events.show', $event) }}" class="btn btn-sm btn-info me-1">Event Details</a>
                <a href="{{ route('admin.waiting-lists.index') }}" class="btn btn-sm btn-primary">All Waiting Lists</a>
            </div>
        </div>
        
        <div class="alert alert-info">
            <strong>Queue Summary:</strong>
            Total waiting: {{ $waitingLists->where('status', 'waiting')->count() }} |
            Invited: {{ $waitingLists->where('status', 'invited')->count() }} |
            Expired: {{ $waitingLists->where('status', 'expired')->count() }} |
            Completed: {{ $waitingLists->where('status', 'completed')->count() }}
        </div>
        
        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead>
                    <tr class="text-white">
                        <th>Queue #</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Expires At</th>
                        <th>Joined At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waitingLists as $waiting)
                    <tr class="@if($waiting->status == 'invited') table-warning @endif">
                        <td><span class="badge bg-secondary">#{{ $waiting->queue_number }}</span></td>
                        <td>{{ $waiting->user->name ?? 'N/A' }}</td>
                        <td>{{ $waiting->user->email ?? 'N/A' }}</td>
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
                                @if($waiting->status == 'invited' && \Carbon\Carbon::now()->greaterThan($waiting->expires_at))
                                    <span class="badge bg-danger">EXPIRED</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $waiting->created_at->format('d M Y H:i') }}</td>
                        <td>
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
                                                    <p>Invite <strong>{{ $waiting->user->name }}</strong> to checkout for:</p>
                                                    <p class="text-primary">{{ $event->title }}</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Expiry Hours (for checkout)</label>
                                                        <input type="number" name="expiry_hours" class="form-control bg-dark text-white" value="24" min="1" max="72" required>
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
                        <td colspan="7" class="text-center">No waiting list entries for this event</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($waitingLists->where('status', 'waiting')->count() > 0)
        <div class="mt-3">
            <form action="{{ route('admin.waiting-lists.invite-next', $event) }}" method="POST" class="d-inline" onsubmit="return confirm('Invite the next person in queue?')">
                @csrf
                <button type="submit" class="btn btn-success">Invite Next in Queue</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection