@extends('admin.layouts.master')

@section('title', 'Transaction #' . $transaction->reference_number)

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Transaction Detail: {{ $transaction->reference_number }}</h6>
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-primary">Back to Transactions</a>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="bg-dark rounded p-3 mb-3">
                    <h6 class="text-primary mb-3">Transaction Information</h6>
                    <table class="table table-borderless">
                        <tr><th width="40%">Reference Number</th><td>{{ $transaction->reference_number }}</td></tr>
                        <tr><th>Status</th>
                            <td>
                                @if($transaction->status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($transaction->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>
                        </tr>
                        <tr><th>Total Price</th><td>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td></tr>
                        <tr><th>Transaction Date</th><td>{{ $transaction->created_at->format('d M Y H:i:s') }}</td></tr>
                        @if($transaction->updated_at != $transaction->created_at)
                        <tr><th>Last Updated</th><td>{{ $transaction->updated_at->format('d M Y H:i:s') }}</td></tr>
                        @endif
                        @if($transaction->snap_token)
                        <tr><th>Snap Token</th><td>{{ $transaction->snap_token }}</td></tr>
                        @endif
                    能有
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="bg-dark rounded p-3 mb-3">
                    <h6 class="text-primary mb-3">Customer Information</h6>
                    <table class="table table-borderless">
                        <tr><th>Name</th><td>{{ $transaction->user->name ?? 'N/A' }}</td></tr>
                        <tr><th>Email</th><td>{{ $transaction->user->email ?? 'N/A' }}</td></tr>
                        <tr><th>Phone</th><td>{{ $transaction->user->phone_number ?? '-' }}</td></tr>
                        <tr><th>Country</th><td>{{ $transaction->user->country ?? '-' }}</td></tr>
                    能有
                </div>
            </div>
        </div>
        
        <!-- E-Tickets -->
        <div class="mt-3">
            <h6 class="mb-3">E-Tickets ({{ $transaction->etickets->count() }})</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Ticket Code</th>
                            <th>Event</th>
                            <th>Ticket Type</th>
                            <th>Price</th>
                            <th>Scanned Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaction->etickets as $eticket)
                        <tr>
                            <td>{{ $eticket->ticket_code }}</td>
                            <td>{{ $eticket->ticket->event->title ?? 'N/A' }}</td>
                            <td>{{ $eticket->ticket->name ?? 'N/A' }}</td>
                            <td>Rp {{ number_format($eticket->ticket->price ?? 0, 0, ',', '.') }}</td>
                            <td>
                                @if($eticket->is_scanned)
                                    <span class="badge bg-success">Scanned</span>
                                    <small class="d-block">{{ \Carbon\Carbon::parse($eticket->scanned_at)->format('d M Y H:i') }}</small>
                                @else
                                    <span class="badge bg-secondary">Not Scanned</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">No e-tickets found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Status Update Form -->
        @if($transaction->status == 'pending')
        <div class="mt-4">
            <div class="bg-dark rounded p-3">
                <h6 class="text-primary mb-3">Update Transaction Status</h6>
                <div class="d-flex gap-2">
                    <form action="{{ route('admin.transactions.update-status', $transaction) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="paid">
                        <button type="submit" class="btn btn-success" onclick="return confirm('Mark this transaction as PAID?')">
                            <i class="fa fa-check me-1"></i> Mark as Paid
                        </button>
                    </form>
                    <form action="{{ route('admin.transactions.update-status', $transaction) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="failed">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Mark this transaction as FAILED?')">
                            <i class="fa fa-times me-1"></i> Mark as Failed
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection