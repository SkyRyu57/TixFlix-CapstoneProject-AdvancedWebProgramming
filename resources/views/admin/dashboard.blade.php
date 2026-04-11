@extends('admin.layouts.master')

@section('title', 'Dasbor')

@section('content')
<div class="flex justify-between items-center mb-6 no-print">
    <div>
        <h1 class="text-3xl font-bold">Selamat datang, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-400 mt-1">Ikhtisar platform dan statistik</p>
    </div>
</div>

<!-- Kartu Statistik -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="glass-card rounded-2xl p-6 stat-card">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center"><i data-lucide="calendar" class="w-6 h-6 text-blue-400"></i></div>
            <span class="text-2xl font-bold">{{ number_format($totalEvents ?? 0) }}</span>
        </div>
        <h3 class="text-gray-400 text-sm">Total Event</h3>
        <p class="text-xs text-gray-500 mt-1">Semua event di platform</p>
    </div>
    <div class="glass-card rounded-2xl p-6 stat-card">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-full bg-green-500/20 flex items-center justify-center"><i data-lucide="ticket" class="w-6 h-6 text-green-400"></i></div>
            <span class="text-2xl font-bold">{{ number_format($totalTicketsSold ?? 0) }}</span>
        </div>
        <h3 class="text-gray-400 text-sm">Tiket Terjual</h3>
        <p class="text-xs text-gray-500 mt-1">Total tiket terjual</p>
    </div>
    <div class="glass-card rounded-2xl p-6 stat-card">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-full bg-yellow-500/20 flex items-center justify-center"><i data-lucide="wallet" class="w-6 h-6 text-yellow-400"></i></div>
            <span class="text-2xl font-bold">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</span>
        </div>
        <h3 class="text-gray-400 text-sm">Total Pendapatan</h3>
        <p class="text-xs text-gray-500 mt-1">Dari semua transaksi berhasil</p>
    </div>
    <div class="glass-card rounded-2xl p-6 stat-card">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-full bg-purple-500/20 flex items-center justify-center"><i data-lucide="users" class="w-6 h-6 text-purple-400"></i></div>
            <span class="text-2xl font-bold">{{ number_format($totalOrganizers ?? 0) }}</span>
        </div>
        <h3 class="text-gray-400 text-sm">Penyelenggara</h3>
        <p class="text-xs text-gray-500 mt-1">Penyelenggara aktif</p>
    </div>
</div>

<!-- Grafik dan Info Cepat -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="glass-card rounded-2xl p-6 lg:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold flex items-center gap-2"><i data-lucide="chart-line" class="w-5 h-5 text-[#ff2d55]"></i> Tren Pendapatan</h2>
            <div class="flex gap-2">
                <select id="chartTypeSelect" class="bg-black/30 border border-white/20 rounded-lg px-3 py-1 text-sm text-white">
                    <option value="line">Grafik Garis</option>
                    <option value="bar">Grafik Batang</option>
                    <option value="pie">Grafik Lingkaran (per Event)</option>
                </select>
            </div>
        </div>
        <canvas id="revenueChart" height="250"></canvas>
    </div>
    <div class="glass-card rounded-2xl p-6">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2"><i data-lucide="bell" class="w-5 h-5 text-yellow-400"></i> Notifikasi Terbaru</h2>
        <div class="space-y-3 max-h-80 overflow-y-auto">
            @forelse($recentNotifications ?? [] as $notif)
                <div class="p-3 rounded-lg bg-white/5 hover:bg-white/10 transition">
                    <p class="text-sm font-semibold">{{ $notif->title }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ Str::limit($notif->message, 80) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-gray-500 text-center">Tidak ada notifikasi</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Tabel Ringkasan Pengguna dan Event -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="glass-card rounded-2xl p-6">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2"><i data-lucide="users" class="w-5 h-5 text-blue-400"></i> Penyelenggara Terbaru</h2>
        <div class="space-y-3">
            @forelse($recentOrganizers ?? [] as $org)
                <div class="flex justify-between items-center p-3 rounded-lg bg-white/5">
                    <div>
                        <p class="font-semibold">{{ $org->name }}</p>
                        <p class="text-xs text-gray-400">{{ $org->email }}</p>
                    </div>
                    <span class="text-xs text-gray-500">Bergabung: {{ $org->created_at->format('d M Y') }}</span>
                </div>
            @empty
                <p class="text-gray-500 text-center">Belum ada penyelenggara</p>
            @endforelse
        </div>
    </div>
    <div class="glass-card rounded-2xl p-6">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2"><i data-lucide="calendar" class="w-5 h-5 text-green-400"></i> Event Terbaru</h2>
        <div class="space-y-3">
            @forelse($recentEvents ?? [] as $ev)
                <div class="flex justify-between items-center p-3 rounded-lg bg-white/5">
                    <div>
                        <p class="font-semibold">{{ $ev->title }}</p>
                        <p class="text-xs text-gray-400">Oleh: {{ $ev->user->name ?? 'Admin' }}</p>
                    </div>
                    <span class="text-xs text-gray-500">{{ $ev->created_at->format('d M Y') }}</span>
                </div>
            @empty
                <p class="text-gray-500 text-center">Belum ada event</p>
            @endforelse
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari controller
        const monthLabels = @json($monthLabels ?? []);
        const revenueData = @json($revenueData ?? []);
        const eventRevenueData = @json($eventRevenueData ?? []);
        
        const canvas = document.getElementById('revenueChart');
        const ctx = canvas.getContext('2d');
        let currentChart = null;
        
        function showMessage(message) {
            if (currentChart) {
                currentChart.destroy();
                currentChart = null;
            }
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.font = '14px "Plus Jakarta Sans", sans-serif';
            ctx.fillStyle = '#9ca3af';
            ctx.textAlign = 'center';
            ctx.fillText(message, canvas.width / 2, canvas.height / 2);
        }
        
        function renderChart(type) {
            if (type === 'pie') {
                const labels = Object.keys(eventRevenueData);
                const data = Object.values(eventRevenueData);
                if (labels.length === 0) {
                    showMessage('Tidak ada data untuk grafik lingkaran');
                    return;
                }
                if (currentChart) currentChart.destroy();
                currentChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: ['#ff2d55', '#ff5e3a', '#5946ea', '#10b981', '#f59e0b']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { position: 'bottom', labels: { color: '#fff' } }
                        }
                    }
                });
            } else {
                if (monthLabels.length === 0 || revenueData.length === 0) {
                    showMessage('Tidak ada data pendapatan');
                    return;
                }
                if (currentChart) currentChart.destroy();
                currentChart = new Chart(ctx, {
                    type: type,
                    data: {
                        labels: monthLabels,
                        datasets: [{
                            label: 'Pendapatan (Rp)',
                            data: revenueData,
                            borderColor: '#ff2d55',
                            backgroundColor: type === 'line' ? 'rgba(255,45,85,0.1)' : '#ff2d55',
                            tension: 0.3,
                            fill: type === 'line'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(val) {
                                        return 'Rp ' + val.toLocaleString();
                                    },
                                    color: '#fff'
                                },
                                grid: { color: 'rgba(255,255,255,0.1)' }
                            },
                            x: {
                                ticks: { color: '#fff' },
                                grid: { color: 'rgba(255,255,255,0.1)' }
                            }
                        }
                    }
                });
            }
        }
        
        const chartTypeSelect = document.getElementById('chartTypeSelect');
        if (chartTypeSelect) {
            renderChart(chartTypeSelect.value);
            chartTypeSelect.addEventListener('change', function(e) {
                renderChart(e.target.value);
            });
        } else {
            renderChart('line');
        }
    });
</script>
@endsection