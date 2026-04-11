<!-- Grid View -->
<div id="gridView" class="row g-4 {{ $viewMode ?? 'd-none' }}" style="{{ (isset($viewMode) && $viewMode == 'grid') ? '' : 'display: flex;' }}">
    @forelse($events as $event)
    <div class="col-md-4 col-lg-3">
        <div class="card stat-card h-100">
            <div class="position-relative">
                @if($event->banner)
                    <img src="{{ asset('storage/' . $event->banner) }}" class="card-img-top" style="height: 160px; object-fit: cover;">
                @else
                    <div style="height: 160px; background: linear-gradient(135deg, #1e293b, #0f172a); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                    </div>
                @endif
                <div class="position-absolute top-0 end-0 p-2" style="z-index: 10; pointer-events: none;">
                    <input type="checkbox" class="form-check-input event-checkbox" data-id="{{ $event->id }}" style="pointer-events: auto; width: 20px; height: 20px;">
                </div>
            </div>
            <div class="card-body">
                <h6 class="card-title fw-bold">{{ Str::limit($event->title, 40) }}</h6>
                <p class="text-muted small mb-1"><i class="fas fa-calendar-alt me-1"></i> {{ Carbon\Carbon::parse($event->start_date)->translatedFormat('d M Y') }}</p>
                <p class="text-muted small"><i class="fas fa-map-marker-alt me-1"></i> {{ Str::limit($event->location, 30) }}</p>
                <span class="badge {{ $event->status == 'published' ? 'bg-success' : ($event->status == 'pending' ? 'bg-warning' : ($event->status == 'rejected' ? 'bg-danger' : 'bg-secondary')) }}">
                    {{ ucfirst($event->status) }}
                </span>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">Belum ada event.</div>
    @endforelse
</div>

<!-- List View -->
<div id="listView" class="d-none">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr><th><input type="checkbox" id="selectAllCheckbox"></th><th>Banner</th><th>Judul</th><th>Tanggal Mulai</th><th>Lokasi</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr>
                    <td><input type="checkbox" class="form-check-input event-checkbox-list" data-id="{{ $event->id }}"></td>
                    <td>@if($event->banner)<img src="{{ asset('storage/' . $event->banner) }}" width="50" height="50" style="object-fit: cover; border-radius: 8px;">@else<div style="width:50px;height:50px;background:#334155;display:flex;align-items:center;justify-content:center;"><i class="fas fa-calendar-alt text-muted"></i></div>@endif</td>
                    <td class="fw-semibold">{{ Str::limit($event->title, 50) }}</td>
                    <td>{{ Carbon\Carbon::parse($event->start_date)->translatedFormat('d M Y') }}</td>
                    <td>{{ Str::limit($event->location, 30) }}</td>
                    <td><span class="badge {{ $event->status == 'published' ? 'bg-success' : ($event->status == 'pending' ? 'bg-warning' : ($event->status == 'rejected' ? 'bg-danger' : 'bg-secondary')) }}">{{ ucfirst($event->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4">Tidak ada event.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $events->appends(request()->query())->links() }}
</div>