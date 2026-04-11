@extends('organizer.layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Dashboard</h2>
        <p class="fw-light">Selamat Datang, {{ auth()->user()->name }}</p>
    </div>
</div>

<!-- Statistik Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <a href="{{ route('organizer.events.index') }}" class="text-decoration-none" id="linkTotalRevenue">
            <div class="stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted">Total Pendapatan</span>
                        <h3 class="mt-2 mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                    </div>
                    <i class="fas fa-chart-line fa-2x text-primary"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('organizer.events.index') }}" class="text-decoration-none" id="linkTotalEvents">
            <div class="stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted">Total Event</span>
                        <h3 class="mt-2 mb-0">{{ $totalEvents }}</h3>
                    </div>
                    <i class="fas fa-calendar-alt fa-2x text-success"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('organizer.events.index') }}" class="text-decoration-none" id="linkAvgSold">
            <div class="stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted">Rata-rata Tiket Terjual per Event</span>
                        <h3 class="mt-2 mb-0">{{ number_format($avgSoldPerEvent, 2) }}</h3>
                    </div>
                    <i class="fas fa-ticket-alt fa-2x text-warning"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Chart Penjualan Tiket (Bulan Berjalan) -->
<div class="card table-custom mt-4">
    <div class="card-header bg-transparent border-secondary">
        <h5 class="mb-0">📈 Penjualan Tiket ({{ Carbon\Carbon::now()->translatedFormat('F Y') }})</h5>
    </div>
    <div class="card-body" style="min-height: 420px;">
        <canvas id="ticketSalesChart" style="width: 100%; height: 100%;"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('ticketSalesChart');
        if (!canvas) return;

        canvas.style.width = '100%';
        canvas.style.height = '100%';

        const labels = @json($chartLabels ?? []);
        const data = @json($chartValues ?? []);
        const fullDates = @json($chartLabelsFull ?? []);

        if (labels.length === 0) return;

        const ctx = canvas.getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tiket Terjual',
                    data: data,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249,115,22,0.1)',
                    borderWidth: 2.5,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#f97316',
                    pointBorderColor: '#ffffff',
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBorderWidth: 1.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: '#f1f5f9', font: { family: 'Poppins', size: 12 } } },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#f1f5f9',
                        bodyColor: '#cbd5e1',
                        borderColor: '#f97316',
                        borderWidth: 1,
                        callbacks: {
                            label: (ctx) => `${ctx.raw} tiket`,
                            title: (tooltipItems) => {
                                const idx = tooltipItems[0].dataIndex;
                                if (fullDates && fullDates[idx]) return fullDates[idx];
                                return `Hari ke-${labels[idx]}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#334155' },
                        ticks: { color: '#cbd5e1', stepSize: 1, callback: (val) => val + ' tiket' },
                        title: { display: true, text: 'Jumlah Tiket Terjual', color: '#94a3b8' }
                    },
                    x: {
                        ticks: { color: '#cbd5e1', maxRotation: 45, autoSkip: true },
                        title: { display: true, text: 'Tanggal (Hari ke-)', color: '#94a3b8' }
                    }
                },
                animation: { duration: 1000, easing: 'easeInOutQuart' }
            }
        });
    });
</script>
@endpush