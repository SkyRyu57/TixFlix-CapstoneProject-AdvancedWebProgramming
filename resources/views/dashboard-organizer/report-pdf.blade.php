<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan - {{ $organizer_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
            background: white;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #ff2d55;
        }
        
        .header h1 {
            color: #ff2d55;
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .header .organizer-name {
            font-size: 16px;
            font-weight: bold;
            color: #555;
            margin-top: 10px;
        }
        
        .header .date {
            font-size: 11px;
            color: #777;
            margin-top: 5px;
        }
        
        /* Summary Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            border: 1px solid #e0e0e0;
        }
        
        .summary-card .label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        
        .summary-card .value {
            font-size: 22px;
            font-weight: bold;
            color: #ff2d55;
        }
        
        /* Tables */
        .event-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .event-table th {
            background: #ff2d55;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        
        .event-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 11px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .total-row {
            background: #f0f0f0;
            font-weight: bold;
        }
        
        /* Ticket Details Table */
        .ticket-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        
        .ticket-table th {
            background: #e0e0e0;
            color: #333;
            padding: 8px;
            font-size: 10px;
            text-align: left;
        }
        
        .ticket-table td {
            padding: 6px 8px;
            font-size: 10px;
            border-bottom: 1px solid #eee;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #999;
        }
        
        /* Event Section */
        .event-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .event-title {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 10px;
            border-left: 4px solid #ff2d55;
        }
        
        .event-title h3 {
            color: #333;
            font-size: 14px;
        }
        
        .event-meta {
            display: flex;
            gap: 20px;
            margin-top: 5px;
            font-size: 10px;
            color: #666;
        }
        
        /* Print Button */
        .print-btn-container {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .print-btn {
            background: #ff2d55;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 30px;
            font-size: 14px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .print-btn:hover {
            background: #ff5e3a;
            transform: scale(1.05);
        }
        
        @media print {
            .print-btn-container {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .summary-card, .event-table, .ticket-table {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="print-btn-container">
        <button class="print-btn" onclick="window.print()">
            🖨️ Print / Save as PDF
        </button>
    </div>
    
    <div class="header">
        <h1>Tixflix</h1>
        <p>Laporan Penjualan Tiket</p>
        <div class="organizer-name">{{ $organizer_name }}</div>
        <div class="organizer-name">{{ $organizer_email }}</div>
        <div class="date">Dicetak: {{ $generated_at->format('d F Y H:i') }} WIB</div>
    </div>
    
    <!-- Summary -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total Events</div>
            <div class="value">{{ $total_events }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Tickets Sold</div>
            <div class="value">{{ number_format($total_tickets_sold) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Gross Revenue</div>
            <div class="value">Rp {{ number_format($total_revenue, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Net Income</div>
            <div class="value">Rp {{ number_format($total_net_income, 0, ',', '.') }}</div>
        </div>
    </div>
    
    <!-- Event Details -->
    <h2 style="margin-bottom: 15px;">📊 Detail Penjualan per Event</h2>
    
    <table class="event-table">
        <thead>
            <tr>
                <th>Event</th>
                <th>Kategori</th>
                <th>Tanggal</th>
                <th class="text-right">Tiket Terjual</th>
                <th class="text-right">Pendapatan</th>
                <th class="text-right">Fee (10%)</th>
                <th class="text-right">Net Income</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
            <tr>
                <td>{{ $event['title'] }}</td>
                <td>{{ $event['category'] }}</td>
                <td>{{ \Carbon\Carbon::parse($event['start_date'])->format('d M Y') }}</td>
                <td class="text-right">{{ number_format($event['tickets_sold']) }}</td>
                <td class="text-right">Rp {{ number_format($event['revenue'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($event['platform_fee'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($event['net_income'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right"><strong>TOTAL</strong></td>
                <td class="text-right"><strong>{{ number_format($total_tickets_sold) }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($total_revenue, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($total_platform_fee, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($total_net_income, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
    
    <!-- Per Event Breakdown with Ticket Types -->
    @foreach($events as $event)
    @if(isset($event['ticket_details']) && count($event['ticket_details']) > 0)
    <div class="event-section">
        <div class="event-title">
            <h3>{{ $event['title'] }}</h3>
            <div class="event-meta">
                <span>📅 {{ \Carbon\Carbon::parse($event['start_date'])->format('d F Y, H:i') }} WIB</span>
                <span>📍 {{ $event['location'] }}</span>
            </div>
        </div>
        
        <table class="ticket-table">
            <thead>
                <tr>
                    <th>Tipe Tiket</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Stok Awal</th>
                    <th class="text-right">Terjual</th>
                    <th class="text-right">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event['ticket_details'] as $ticket)
                <tr>
                    <td>{{ $ticket['name'] }}</td>
                    <td class="text-right">Rp {{ number_format($ticket['price'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($ticket['stock']) }}</td>
                    <td class="text-right">{{ number_format($ticket['sold']) }}</td>
                    <td class="text-right">Rp {{ number_format($ticket['revenue'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    @endforeach
    
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem Tixflix</p>
        <p>&copy; {{ date('Y') }} Tixflix - All Rights Reserved</p>
        <p>*Laporan ini merupakan bukti sah penjualan tiket</p>
    </div>
    
    <script>
        // Auto trigger print? Optional - uncomment if you want auto print
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // }
    </script>
</body>
</html>