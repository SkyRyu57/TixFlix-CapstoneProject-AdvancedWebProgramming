<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Event - TixFlix</title>
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            background: white;
            color: #0f172a;
            padding: 2rem;
            margin: 0;
        }
        .print-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f97316;
        }
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
            color: #f97316;
        }
        .header p {
            margin: 0.25rem 0;
            color: #475569;
            font-size: 0.875rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
            font-size: 0.875rem;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 0.75rem;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f1f5f9;
            font-weight: 600;
            color: #0f172a;
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-published { background-color: #dcfce7; color: #15803d; }
        .badge-pending { background-color: #fef9c3; color: #854d0e; }
        .badge-rejected { background-color: #fee2e2; color: #b91c1c; }
        .badge-cancelled { background-color: #e2e3e5; color: #383d41; }
        .footer {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.75rem;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 1rem;
        }
        .no-print {
            text-align: right;
            margin-bottom: 1rem;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .badge {
                border: 1px solid #000;
                background: none !important;
                color: #000 !important;
            }
        }
    </style>
</head>
<body>
<div class="print-container">
    <div class="no-print">
        <button onclick="window.print()" style="background: #f97316; border: none; color: white; padding: 8px 16px; border-radius: 8px; cursor: pointer; margin-right: 8px;">🖨️ Cetak / Simpan PDF</button>
        <button onclick="window.close()" style="background: #64748b; border: none; color: white; padding: 8px 16px; border-radius: 8px; cursor: pointer;">❌ Tutup</button>
    </div>

    <div class="header">
        <h1>Laporan Event</h1>
        <p>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</p>
        @if(request('status') && request('status') != 'all')
            <p>Status: {{ ucfirst(request('status')) }}</p>
        @endif
        @if(request('search'))
            <p>Pencarian: {{ request('search') }}</p>
        @endif
    </div>

    @if($events->count() > 0)
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul Event</th>
                    <th>Organizer</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Tanggal Mulai</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                <tr>
                    <td>{{ $event->id }}</td>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->user->name ?? '-' }}</td>
                    <td>{{ $event->category->name ?? '-' }}</td>
                    <td>{{ $event->location }}</td>
                    <td>{{ \Carbon\Carbon::parse($event->start_date)->translatedFormat('d F Y') }}</td>
                    <td>
                        <span class="badge badge-{{ $event->status }}">
                            {{ ucfirst($event->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <p style="text-align: center; color: #ef4444;">Tidak ada data event yang sesuai dengan filter.</p>
    @endif

    <div class="footer">
        Dicetak oleh: {{ auth()->user()->name }} | TixFlix System
    </div>
</div>
</body>
</html>