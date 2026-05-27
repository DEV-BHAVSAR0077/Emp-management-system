<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ledger History - {{ $agencyVendor->name }}</title>
    <style>
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 11px; 
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .header h1 { 
            margin: 0 0 5px 0; 
            font-size: 24px; 
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p { 
            margin: 0; 
            font-size: 14px; 
            color: #64748b;
        }
        .info-section { 
            margin-bottom: 25px; 
            display: table;
            width: 100%;
        }
        .info-box {
            display: table-cell;
            width: 50%;
        }
        .info-box p {
            margin: 8px 0;
            font-size: 13px;
        }
        .info-label {
            font-weight: bold;
            color: #475569;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
        }
        th, td { 
            border: 1px solid #cbd5e1; 
            padding: 10px 8px; 
            text-align: left; 
        }
        th { 
            background-color: #f8fafc; 
            color: #1e293b;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        tbody tr:nth-child(even) {
            background-color: #f1f5f9;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .success { color: #15803d; font-weight: bold; }
        .danger { color: #b91c1c; font-weight: bold; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Ledger Statement</h1>
        <p>Generated on {{ \Carbon\Carbon::now()->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') }}</p>
    </div>

    <div class="info-section">
        <div class="info-box">
            <p><span class="info-label">Agency / Vendor:</span> {{ $agencyVendor->name }}</p>
            @if($startDate || $endDate)
                <p><span class="info-label">Period:</span> 
                    {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Start' }} 
                    to 
                    {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : 'End' }}
                </p>
            @else
                <p><span class="info-label">Period:</span> All time</p>
            @endif
        </div>
        <div class="info-box" style="text-align: right;">
            <p><span class="info-label">Closing Balance:</span> 
                @php $bal = (float)$agencyVendor->balance; @endphp
                @if ($bal < 0)
                    <span class="success">Overpaid: +₹{{ number_format(abs($bal), 2) }}</span>
                @else
                    <span class="danger">To Pay: ₹{{ number_format($bal, 2) }}</span>
                @endif
            </p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Transaction</th>
                <th class="text-right">Debit (₹)</th>
                <th class="text-right">Credit (₹)</th>
                <th class="text-right">Balance (₹)</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ledgers as $ledger)
                <tr>
                    <td>{{ $ledger['date_formatted'] }}</td>
                    <td>{{ $ledger['type_label'] }}</td>
                    <td class="text-right danger">{{ $ledger['debit_formatted'] !== '-' ? $ledger['debit_formatted'] : '-' }}</td>
                    <td class="text-right success">{{ $ledger['credit_formatted'] !== '-' ? $ledger['credit_formatted'] : '-' }}</td>
                    <td class="text-right">{{ str_replace('₹', '', $ledger['balance_formatted']) }}</td>
                    <td>{{ $ledger['system_note'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No ledger entries found for the selected period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        This is a system generated ledger statement.
    </div>
</body>
</html>
