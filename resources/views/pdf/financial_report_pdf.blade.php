<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        
        /* Header Section */
        .header {
            width: 100%;
            border-bottom: 2px solid #004b87;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header-logo {
            float: left;
            width: 150px;
        }
        .header-logo img {
            max-width: 150px;
            max-height: 60px;
            object-fit: contain;
        }
        .header-info {
            float: right;
            text-align: right;
            color: #555555;
            font-size: 12px;
            line-height: 1.5;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #004b87;
            margin-bottom: 5px;
        }
        
        /* Report Title & Info */
        .report-title-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .report-title {
            font-size: 24px;
            font-weight: bold;
            color: #004b87;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .report-period {
            font-size: 14px;
            color: #666666;
            font-weight: 500;
        }
        
        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        thead th {
            background-color: #004b87;
            color: #ffffff;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #003366;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tbody tr:hover {
            background-color: #f1f5f9;
        }
        
        /* Typography Utilities */
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        
        /* Colors for amounts */
        .amount {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            font-size: 15px;
        }
        .text-danger {
            color: #dc3545;
        }
        .text-success {
            color: #28a745;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-expense {
            background-color: #f8d7da;
            color: #721c24;
        }
        .badge-payment {
            background-color: #d4edda;
            color: #155724;
        }
        
        /* Summary Section */
        .summary-container {
            width: 40%;
            float: right;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
        }
        .summary-title {
            font-size: 16px;
            font-weight: bold;
            color: #004b87;
            margin: 0 0 15px 0;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }
        .summary-table {
            margin-bottom: 0;
        }
        .summary-table th, .summary-table td {
            padding: 8px 5px;
            border-bottom: 1px dashed #cccccc;
        }
        .summary-table tr:last-child th, .summary-table tr:last-child td {
            border-bottom: none;
        }
        .summary-total {
            font-size: 16px;
            font-weight: bold;
        }
        .summary-total th {
            color: #004b87;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 30px;
            font-size: 10px;
            color: #999999;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
        }
        
        .clear {
            clear: both;
        }
        
        /* Page Numbers (DomPDF specific) */
        .page-number:before {
            content: "Page " counter(page);
        }
    </style>
</head>
<body>

    <!-- Footer -->
    <div class="footer">
        Generated on {{ now()->format('M d, Y H:i:s') }} | <span class="page-number"></span>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="header-logo">
            <?php
                $logoPath = public_path('images/logo.jpg');
                if (file_exists($logoPath)) {
                    $logoData = base64_encode(file_get_contents($logoPath));
                    echo '<img src="data:image/jpeg;base64,' . $logoData . '" alt="Company Logo">';
                }
            ?>
        </div>
        <div class="header-info">
            <div class="company-name">Your Company Name</div>
            <div>123 Business Avenue, Suite 100</div>
            <div>New York, NY 10001</div>
            <div>contact@yourcompany.com | (555) 123-4567</div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Report Title -->
    <div class="report-title-section">
        <h1 class="report-title">{{ ucfirst($frequency) }} Financial Report</h1>
        <div class="report-period">Reporting Period: {{ $startDate }} — {{ $endDate }}</div>
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th width="15%">Date</th>
                <th width="15%">Type</th>
                <th width="45%">Description / Category</th>
                <th width="25%" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
                <tr>
                    <td>{{ $entry['date'] }}</td>
                    <td>
                        @if($entry['type'] == 'Expense')
                            <span class="badge badge-expense">Expense</span>
                        @else
                            @php
                                $isCredit = isset($entry['sub_type']) && $entry['sub_type'] == 'Credit';
                            @endphp
                            <span class="badge {{ $isCredit ? 'badge-expense' : 'badge-payment' }}">
                                Payment ({{ $entry['sub_type'] ?? '' }})
                            </span>
                        @endif
                    </td>
                    <td>{{ $entry['description'] }}</td>
                    @php
                        $isDeduction = $entry['type'] == 'Expense' || (isset($entry['sub_type']) && $entry['sub_type'] == 'Credit');
                    @endphp
                    <td class="amount text-right {{ $isDeduction ? 'text-danger' : 'text-success' }}">
                        {{ $isDeduction ? '-' : '+' }} {{ number_format($entry['amount'], 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center font-bold" style="padding: 30px;">
                        No financial activity found for this period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary Box -->
    <div class="summary-container">
        <h3 class="summary-title">Financial Summary</h3>
        <table class="summary-table">
            <tr>
                <th>Total Expenses:</th>
                <td class="amount text-right text-danger">-{{ number_format($expenses, 2) }}</td>
            </tr>
            <tr>
                <th>Total Payments:</th>
                <td class="amount text-right text-success">+{{ number_format($payments, 2) }}</td>
            </tr>
            <tr class="summary-total">
                <th>Net Balance:</th>
                <td class="amount text-right {{ $finalAmount >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $finalAmount < 0 ? '-' : '' }}{{ number_format(abs($finalAmount), 2) }}
                </td>
            </tr>
        </table>
    </div>
    
    <div class="clear"></div>

</body>
</html>
