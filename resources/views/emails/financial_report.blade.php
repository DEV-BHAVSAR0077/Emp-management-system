<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 15px;
        }
        .content {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #0056b3;
            color: #fff;
        }
        .footer {
            text-align: center;
            font-size: 0.9em;
            color: #777;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(file_exists(public_path('images/logo.jpg')))
                <a href="#" style="text-decoration: none; border: none; pointer-events: none; cursor: default;">
                    <img src="{{ $message->embed(public_path('images/logo.jpg')) }}" alt="Logo" style="pointer-events: none; user-select: none;">
                </a>
            @endif
            <h2>{{ ucfirst($reportData['frequency'] ?? 'Weekly') }} Financial Report</h2>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>Please find attached the {{ strtolower($reportData['frequency'] ?? 'weekly') }} financial report for the period <strong>{{ $reportData['start_date'] }}</strong> to <strong>{{ $reportData['end_date'] }}</strong>.</p>
            
            <h3>Summary</h3>
            <table>
                <tr>
                    <th>Total Expenses</th>
                    <td>{{ number_format($reportData['expenses'], 2) }}</td>
                </tr>
                <tr>
                    <th>Total Payments</th>
                    <td>{{ number_format($reportData['payments'], 2) }}</td>
                </tr>
                <tr style="font-weight: bold; background-color: #e9ecef;">
                    <th>Final Amount (Payments - Expenses)</th>
                    <td>{{ number_format($reportData['final_amount'], 2) }}</td>
                </tr>
            </table>

            <p>A detailed PDF containing all entries for this period has been attached to this email.</p>
            <p>Thank you.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
