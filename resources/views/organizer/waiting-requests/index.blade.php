@extends('organizer.layouts.master')
@section('title', 'Waiting Requests')
@section('content')
<div class="container-fluid px-4 py-4">
    <h2 class="mb-4">Waiting Requests per Event</h2>
    <div class="row">
        @forelse($events as $event)
        <div class="col-md-4 mb-3">
            <div class="glass-card rounded-2xl p-4">
                <h5>{{ $event->title }}</h5>
                <p>Pending: <strong>{{ $event->waiting_requests_count }}</strong></p>
                <a href="{{ route('organizer.waiting-requests.show', $event) }}" class="btn btn-primary btn-sm">Manage</a>
            </div>
        </div>
        @empty
        <div class="col-12">
            <p class="text-muted">Tidak ada waiting request.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection