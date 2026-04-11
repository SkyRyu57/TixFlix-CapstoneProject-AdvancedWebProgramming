@extends('organizer.layouts.master')
@section('title', 'Waiting Requests - ' . $event->title)
@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Waiting Requests: {{ $event->title }}</h2>
        <a href="{{ route('organizer.waiting-requests.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="glass-card rounded-2xl p-4 mb-4">
        <form method="POST" action="{{ route('organizer.waiting-requests.invite', $event) }}" class="row g-3">
            @csrf
            <div class="col-auto">
                <label class="form-label text-white">Jumlah undangan</label>
                <input type="number" name="slots" class="form-control bg-dark text-white" min="1" value="5" required>
            </div>
            <div class="col-auto align-self-end">
                <button type="submit" class="btn btn-primary">Undang N Teratas</button>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jumlah</th>
                        <th>Catatan</th>
                        <th>Tgl Request</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td>{{ $req->user->name }}</td>
                        <td>{{ $req->user->email }}</td>
                        <td>{{ $req->quantity }}</td>
                        <td>{{ $req->notes ?? '-' }}</td>
                        <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge bg-{{ $req->status == 'pending' ? 'warning' : 'info' }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td>
                            @if($req->status == 'invited')
                            <form method="POST" action="{{ route('organizer.waiting-requests.cancel', $req) }}">
                                @csrf
                                <button class="btn btn-sm btn-warning">Batalkan</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada pending request.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection