<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Event - TixFlix</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #0b1120;
            padding: 2rem;
            color: #e2e8f0;
        }

        /* Container utama laporan */
        .report-container {
            max-width: 1200px;
            margin: 0 auto;
            background: #1e293b;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            border: 1px solid #334155;
        }

        /* Header dengan gradien */
        .report-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 2rem;
            border-bottom: 2px solid #f97316;
        }

        .event-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #f97316;
            margin-bottom: 0.5rem;
        }

        .event-meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .event-meta i {
            margin-right: 0.5rem;
            color: #f97316;
        }

        /* Ringkasan dalam bentuk grid card */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            padding: 2rem;
            background: #0f172a;
            border-bottom: 1px solid #334155;
        }

        .summary-card {
            background: #1e293b;
            border-radius: 1rem;
            padding: 1rem 1.5rem;
            border-left: 4px solid #f97316;
        }

        .summary-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }

        .summary-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #f1f5f9;
        }

        /* Tabel */
        .table-wrapper {
            padding: 1.5rem 2rem;
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .data-table th {
            text-align: left;
            padding: 1rem 0.75rem;
            background: #0f172a;
            color: #cbd5e1;
            font-weight: 600;
            border-bottom: 1px solid #334155;
        }

        .data-table td {
            padding: 0.85rem 0.75rem;
            border-bottom: 1px solid #334155;
            color: #e2e8f0;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover td {
            background: rgba(249, 115, 22, 0.05);
        }

        /* Baris total di akhir tabel */
        .total-row td {
            font-weight: 700;
            background: #0f172a;
            border-top: 2px solid #f97316;
            padding: 1rem 0.75rem;
        }

        .total-label {
            text-align: right;
            font-weight: 700;
            color: #f1f5f9;
        }

        /* Footer */
        .report-footer {
            padding: 1.5rem 2rem;
            background: #0f172a;
            border-top: 1px solid #334155;
            font-size: 0.7rem;
            color: #64748b;
            text-align: center;
        }

        /* Tombol aksi */
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            font-family: 'Inter', sans-serif;
        }

        .btn-print {
            background: #f97316;
            color: white;
        }

        .btn-print:hover {
            background: #ea580c;
        }

        .btn-close {
            background: #334155;
            color: #e2e8f0;
        }

        .btn-close:hover {
            background: #475569;
        }

        /* Badge status */
        .badge {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        .badge-published {
            background: #10b981;
            color: white;
        }

        .badge-pending {
            background: #f59e0b;
            color: white;
        }

        .badge-cancelled {
            background: #ef4444;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            .summary-grid {
                grid-template-columns: 1fr;
            }
            .table-wrapper {
                padding: 1rem;
            }
            .data-table th, .data-table td {
                padding: 0.5rem;
            }
        }

        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .report-container {
                box-shadow: none;
                border-radius: 0;
                margin: 0;
                border: none;
            }
            .report-header {
                background: white;
                border-bottom: 2px solid #f97316;
            }
            .event-title {
                color: #f97316;
            }
            .summary-grid {
                background: white;
            }
            .summary-card {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
            }
            .summary-value {
                color: #0f172a;
            }
            .table-wrapper {
                padding: 0;
            }
            .data-table th {
                background: #f1f5f9;
                color: #0f172a;
            }
            .data-table td {
                color: #334155;
            }
            .data-table tr:hover td {
                background: none;
            }
            .total-row td {
                background: #f8fafc;
            }
            .action-buttons, .report-footer {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="action-buttons">
        <button class="btn btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak / Simpan PDF
        </button>
        <button class="btn btn-close" onclick="window.close()">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>

    <div class="report-container">
        <!-- Header Laporan -->
        <div class="report-header">
            <div class="event-title">{{ $events->first()->title ?? 'Laporan Event' }}</div>
            <div class="event-meta">
                <span><i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($events->first()->start_date ?? now())->translatedFormat('d F Y') }}</span>
                <span><i class="fas fa-map-marker-alt"></i> {{ $events->first()->location ?? 'Lokasi tidak tersedia' }}</span>
                <span><i class="fas fa-chart-line"></i> Laporan Penjualan Tiket</span>
            </div>
        </div>

        <!-- Ringkasan Data (KPI Cards) -->
        @php
            $totalRevenue = 0;
            $totalSoldTickets = 0;
            $totalAvailableTickets = 0;
            foreach($events as $event) {
                foreach($event->tickets as $ticket) {
                    $sold = \App\Models\Eticket::where('ticket_id', $ticket->id)->count();
                    $totalSoldTickets += $sold;
                    $totalAvailableTickets += $ticket->stock;
                    $totalRevenue += $sold * $ticket->price;
                }
            }
            $occupancyRate = $totalAvailableTickets > 0 ? round(($totalSoldTickets / $totalAvailableTickets) * 100, 2) : 0;
        @endphp

        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-label">Total Pendapatan</div>
                <div class="summary-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Tiket Terjual</div>
                <div class="summary-value">{{ number_format($totalSoldTickets) }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Sisa Tiket</div>
                <div class="summary-value">{{ number_format($totalAvailableTickets) }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Tingkat Okupansi</div>
                <div class="summary-value">{{ $occupancyRate }}%</div>
            </div>
        </div>

        <!-- Tabel Detail Event -->
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Kategori</th>
                        <th>Tiket</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Terjual</th>
                        <th>Sisa</th>
                        <th>Pendapatan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        @php
                            $eventSold = 0;
                            $eventRevenue = 0;
                        @endphp
                        @foreach($event->tickets as $ticket)
                            @php
                                $sold = \App\Models\Eticket::where('ticket_id', $ticket->id)->count();
                                $remaining = $ticket->stock - $sold;
                                $revenue = $sold * $ticket->price;
                                $eventSold += $sold;
                                $eventRevenue += $revenue;
                            @endphp
                            <tr>
                                @if($loop->first)
                                    <td rowspan="{{ $event->tickets->count() }}" style="vertical-align: middle;">
                                        <strong>{{ $event->title }}</strong><br>
                                        <span style="font-size: 0.7rem; color: #94a3b8;">{{ \Carbon\Carbon::parse($event->start_date)->translatedFormat('d M Y') }}</span>
                                    </td>
                                @endif
                                <td>{{ $event->category->name ?? '-' }}</td>
                                <td>{{ $ticket->name }}</td>
                                <td>Rp {{ number_format($ticket->price, 0, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($ticket->stock) }}</td>
                                <td class="text-center">{{ number_format($sold) }}</td>
                                <td class="text-center">{{ number_format($remaining) }}</td>
                                <td>Rp {{ number_format($revenue, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-{{ $event->status }}">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        <!-- Baris subtotal per event -->
                        <tr class="total-row">
                            <td colspan="5" class="total-label">Subtotal {{ $event->title }}</td>
                            <td class="text-center"><strong>{{ number_format($eventSold) }}</strong></td>
                            <td class="text-center"><strong>{{ number_format($event->tickets->sum('stock') - $eventSold) }}</strong></td>
                            <td colspan="2"><strong>Rp {{ number_format($eventRevenue, 0, ',', '.') }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">Tidak ada data event yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- Footer tabel total keseluruhan -->
                @if($events->count() > 0)
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="5" class="total-label">TOTAL KESELURUHAN</td>
                            <td class="text-center"><strong>{{ number_format($totalSoldTickets) }}</strong></td>
                            <td class="text-center"><strong>{{ number_format($totalAvailableTickets) }}</strong></td>
                            <td colspan="2"><strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <!-- Footer -->
        <div class="report-footer">
            <p>Laporan dibuat secara otomatis oleh sistem TixFlix pada {{ now()->translatedFormat('d F Y H:i:s') }}</p>
            <p>&copy; {{ date('Y') }} TixFlix - All rights reserved.</p>
        </div>
    </div>

    <!-- Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</body>
</html>