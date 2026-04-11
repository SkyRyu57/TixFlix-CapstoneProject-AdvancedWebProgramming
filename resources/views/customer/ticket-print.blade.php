<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Ticket - {{ $eticket->ticket_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        /* Ticket Container */
        .ticket {
            max-width: 800px;
            width: 100%;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        /* Ticket Header */
        .ticket-header {
            background: linear-gradient(135deg, #ff2d55 0%, #ff5e3a 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .ticket-header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .ticket-header p {
            font-size: 12px;
            opacity: 0.9;
        }
        
        /* QR Code Section */
        .qr-section {
            text-align: center;
            padding: 30px;
            background: white;
            border-bottom: 2px dashed #e0e0e0;
        }
        
        .qr-code {
            display: inline-block;
            background: white;
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .qr-code img {
            width: 180px;
            height: 180px;
        }
        
        .ticket-code {
            margin-top: 15px;
            font-family: monospace;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            background: #f0f0f0;
            display: inline-block;
            padding: 8px 20px;
            border-radius: 30px;
            color: #ff2d55;
        }
        
        /* Ticket Details */
        .ticket-details {
            padding: 25px;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 15px;
        }
        
        .detail-label {
            width: 120px;
            font-weight: bold;
            color: #666;
        }
        
        .detail-value {
            flex: 1;
            color: #333;
        }
        
        .event-title {
            font-size: 20px;
            font-weight: bold;
            color: #ff2d55;
            margin-bottom: 15px;
        }
        
        /* Price Section */
        .price-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .total-price {
            border-top: 2px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 18px;
            color: #ff2d55;
        }
        
        /* Footer */
        .ticket-footer {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #e0e0e0;
        }
        
        /* Buttons */
        .action-buttons {
            text-align: center;
            margin-top: 20px;
        }
        
        .btn-print {
            background: #ff2d55;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-size: 14px;
            cursor: pointer;
            margin: 0 10px;
            transition: all 0.3s;
        }
        
        .btn-print:hover {
            background: #ff5e3a;
            transform: scale(1.05);
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .action-buttons {
                display: none;
            }
            .ticket {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
        
        /* Warning */
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin-top: 15px;
            font-size: 11px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div>
        <div class="ticket">
            <div class="ticket-header">
                <h1>🎫 Tixflix</h1>
                <p>E-Ticket • Valid Entry Pass</p>
            </div>
            
            <div class="qr-section">
                <div class="qr-code">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ $eticket->ticket_code }}" alt="QR Code">
                </div>
                <div class="ticket-code">{{ $eticket->ticket_code }}</div>
            </div>
            
            <div class="ticket-details">
                <div class="event-title">{{ $eticket->ticket->event->title }}</div>
                
                <div class="detail-row">
                    <div class="detail-label">Event Date</div>
                    <div class="detail-value">{{ \Carbon\Carbon::parse($eticket->ticket->event->start_date)->format('l, d F Y') }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Time</div>
                    <div class="detail-value">{{ \Carbon\Carbon::parse($eticket->ticket->event->start_date)->format('H:i') }} WIB - {{ \Carbon\Carbon::parse($eticket->ticket->event->end_date)->format('H:i') }} WIB</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Location</div>
                    <div class="detail-value">{{ $eticket->ticket->event->location }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Ticket Type</div>
                    <div class="detail-value">{{ $eticket->ticket->name }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Ticket Holder</div>
                    <div class="detail-value">{{ auth()->user()->name }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">{{ auth()->user()->email }}</div>
                </div>
                
                <div class="price-section">
                    <div class="price-row">
                        <span>Ticket Price</span>
                        <span>Rp {{ number_format($eticket->ticket->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="price-row">
                        <span>Platform Fee</span>
                        <span>Rp {{ number_format($eticket->ticket->price * 0.1, 0, ',', '.') }}</span>
                    </div>
                    <div class="price-row total-price">
                        <span>Total Paid</span>
                        <span>Rp {{ number_format($eticket->ticket->price, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                <div class="warning">
                    ⚠️ Important: Please present this e-ticket (printed or on mobile) at the venue entrance. 
                    QR code will be scanned for verification. One ticket per person. No refunds or exchanges.
                </div>
            </div>
            
            <div class="ticket-footer">
                <p>Transaction ID: {{ $eticket->transaction->reference_number ?? 'N/A' }}</p>
                <p>Issued: {{ \Carbon\Carbon::parse($eticket->created_at)->format('d F Y H:i') }} WIB</p>
                <p>© Tixflix - Your Trusted Event Platform</p>
            </div>
        </div>
        
        <div class="action-buttons">
            <button class="btn-print" onclick="window.print()">
                🖨️ Print / Save as PDF
            </button>
            <button class="btn-back" onclick="window.location.href='{{ route('my-tickets') }}'">
                ← Back to My Tickets
            </button>
        </div>
    </div>
</body>
</html>