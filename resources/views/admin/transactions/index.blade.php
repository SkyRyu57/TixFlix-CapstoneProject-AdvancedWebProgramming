@extends('admin.layouts.master')

@section('title', 'All Transactions')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">All Transactions</h6>
            <div>
                <a href="{{ route('admin.transactions.index') }}?status=paid" class="btn btn-sm btn-success me-1">Paid</a>
                <a href="{{ route('admin.transactions.index') }}?status=pending" class="btn btn-sm btn-warning me-1">Pending</a>
                <a href="{{ route('admin.transactions.index') }}?status=failed" class="btn btn-sm btn-danger me-1">Failed</a>
                <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-secondary">All</a>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead>
                    <tr class="text-white">
                        <th>ID</th>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>{{ $transaction->reference_number }}</td>
                        <td>{{ $transaction->user->name ?? 'N/A' }}<br>
                            <small class="text-muted">{{ $transaction->user->email ?? '' }}</small>
                        </td>
                        <td>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                        <td>
                            @if($transaction->status == 'paid')
                                <span class="badge bg-success text-white">Paid</span>
                            @elseif($transaction->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-danger text-white">Failed</span>
                            @endif
                        </td>
                        <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.transactions.show', $transaction) }}" class="btn btn-sm btn-primary">Detail</a>
                            @if($transaction->status == 'pending')
                                <form action="{{ route('admin.transactions.update-status', $transaction) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="paid">
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark as paid?')">Mark Paid</button>
                                </form>
                                <form action="{{ route('admin.transactions.update-status', $transaction) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="failed">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Mark as failed?')">Mark Failed</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No transactions found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection