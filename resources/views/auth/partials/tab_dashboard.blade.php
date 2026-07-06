{{-- Dashoard Tab --}}

<div id="dashboard-tab" class="tab-content {{ $dashTabActive ? 'active' : '' }}">
    <div class="dash-card">
        <h1>Welcome, {{ $user->name }}! 👋</h1>
        <p style="margin: 0; color:var(--text-muted);">
            You are logged in as <strong>{{ $user->email }}</strong>.
            Your current assigned role is <strong>{{ $user->roleInfo->name ?? 'N/A' }}</strong>. You can navigate the tabs above based on your granted permissions.
        </p>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Card 1: Today's Expense -->
        <div class="dash-card" style="display:flex; flex-direction:column; justify-content:center; padding:1.5rem;">
            <div style="font-size:0.9rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.5rem;">Today's Expense</div>
            <div style="font-size:1.8rem; font-weight:700; color:var(--text-color);">₹{{ number_format($todayExpense, 2) }}</div>
        </div>

        <!-- Card 2: This Month's Expense -->
        <div class="dash-card" style="display:flex; flex-direction:column; justify-content:center; padding:1.5rem;">
            <div style="font-size:0.9rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.5rem;">This Month's Expense</div>
            <div style="font-size:1.8rem; font-weight:700; color:var(--text-color);">₹{{ number_format($monthExpense, 2) }}</div>
        </div>

        <!-- Card 3: This Year's Expense -->
        <div class="dash-card" style="display:flex; flex-direction:column; justify-content:center; padding:1.5rem;">
            <div style="font-size:0.9rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.5rem;">This Year's Expense</div>
            <div style="font-size:1.8rem; font-weight:700; color:var(--text-color);">₹{{ number_format($yearExpense, 2) }}</div>
        </div>

        <!-- Card 4: Total Balance -->
        <div class="dash-card" style="display:flex; flex-direction:column; justify-content:center; padding:1.5rem;">
            <div style="font-size:0.9rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.5rem;">Total of A&V Balance</div>
            @if ($totalBalance < 0)
                <div style="font-size:1.8rem; font-weight:700; color:var(--success);">+₹{{ number_format(abs($totalBalance), 2) }} <span style="font-size:0.8rem; font-weight:500; color:var(--text-muted);">(Overpaid)</span></div>
            @elseif ($totalBalance > 0)
                <div style="font-size:1.8rem; font-weight:700; color:var(--danger);">₹{{ number_format($totalBalance, 2) }} <span style="font-size:0.8rem; font-weight:500; color:var(--text-muted);">(To Pay)</span></div>
            @else
                <div style="font-size:1.8rem; font-weight:700; color:var(--text-muted);">₹0.00 <span style="font-size:0.8rem; font-weight:500; color:var(--text-muted);">(Settled)</span></div>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="dash-card" style="padding:1.5rem; margin-bottom:0;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                <div style="font-size:1.1rem; font-weight:600; color:var(--text-color);">Monthly Vendor Expenses</div>
                <input type="month" id="chartMonthYear" value="{{ \Carbon\Carbon::now()->format('Y-m') }}" style="padding:0.4rem 0.8rem; border-radius:6px; border:1px solid var(--border); font-family:inherit; outline:none;" />
            </div>
            <div style="position: relative; height:350px; width:100%; display:flex; justify-content:center;">
                <canvas id="vendorExpenseChart"></canvas>
                <div id="chart-empty-state" style="display:none; position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); color:var(--text-muted); font-size:0.9rem;">No expenses found for this month.</div>
            </div>
        </div>

        <div class="dash-card" style="padding:1.5rem; margin-bottom:0;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                <div style="font-size:1.1rem; font-weight:600; color:var(--text-color);">Category Expenses (Sub-Categories)</div>
                <input type="month" id="stackedChartMonthYear" value="{{ \Carbon\Carbon::now()->format('Y-m') }}" style="padding:0.4rem 0.8rem; border-radius:6px; border:1px solid var(--border); font-family:inherit; outline:none;" />
            </div>
            <div style="position: relative; height:350px; width:100%; display:flex; justify-content:center;">
                <canvas id="categoryExpenseChart"></canvas>
                <div id="stacked-chart-empty-state" style="display:none; position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); color:var(--text-muted); font-size:0.9rem;">No expenses found for this month.</div>
            </div>
        </div>
    </div>

    <div class="dash-card" style="padding:1.5rem; margin-bottom:2rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <div style="font-size:1.1rem; font-weight:600; color:var(--text-color);">Yearly Expenses by Month</div>
            <select id="lineChartYear" style="padding:0.4rem 0.8rem; border-radius:6px; border:1px solid var(--border); font-family:inherit; outline:none; cursor:pointer;">
                @php $currentYear = \Carbon\Carbon::now()->year; @endphp
                @for ($y = $currentYear + 2; $y >= $currentYear - 10; $y--)
                    <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div style="position: relative; height:350px; width:100%; display:flex; justify-content:center;">
            <canvas id="monthlyExpenseLineChart"></canvas>
            <div id="line-chart-empty-state" style="display:none; position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); color:var(--text-muted); font-size:0.9rem;">No expenses found for this year.</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('vendorExpenseChart');
        if (!ctx) return;
        
        let vendorChart = null;
        const monthYearInput = document.getElementById('chartMonthYear');
        const emptyState = document.getElementById('chart-empty-state');

        function fetchChartData() {
            const val = monthYearInput.value; // e.g. "2026-05"
            if (!val) return;
            
            const parts = val.split('-');
            const year = parts[0];
            const month = parts[1];
            
            fetch(`/dashboard/chart-data?year=${year}&month=${month}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.data.length === 0) {
                    ctx.style.display = 'none';
                    emptyState.style.display = 'block';
                    if (vendorChart) {
                        vendorChart.destroy();
                        vendorChart = null;
                    }
                    return;
                }
                
                ctx.style.display = 'block';
                emptyState.style.display = 'none';

                const chartConfig = {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Total Expenses (₹)',
                            data: data.data,
                            backgroundColor: [
                                '#3b82f6', '#10b981', '#f59e0b', '#ef4444', 
                                '#8b5cf6', '#ec4899', '#14b8a6', '#f97316',
                                '#6366f1', '#84cc16', '#eab308', '#06b6d4',
                                '#3f6212', '#9f1239', '#4338ca', '#be123c'
                            ],
                            borderWidth: 1,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    font: { family: 'Inter', size: 13 },
                                    padding: 15
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let value = context.raw;
                                        return ' ₹' + value.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                                    }
                                }
                            }
                        }
                    }
                };

                if (vendorChart) {
                    vendorChart.data.labels = data.labels;
                    vendorChart.data.datasets[0].data = data.data;
                    vendorChart.update();
                } else {
                    vendorChart = new Chart(ctx, chartConfig);
                }
            })
            .catch(err => console.error('Error fetching chart data:', err));
        }

        monthYearInput.addEventListener('change', fetchChartData);
        fetchChartData();

        // Stacked Bar Chart Logic
        const stackedCtx = document.getElementById('categoryExpenseChart');
        if (stackedCtx) {
            let categoryChart = null;
            const stackedMonthYearInput = document.getElementById('stackedChartMonthYear');
            const stackedEmptyState = document.getElementById('stacked-chart-empty-state');

            function fetchStackedChartData() {
                const val = stackedMonthYearInput.value;
                if (!val) return;
                
                const parts = val.split('-');
                const year = parts[0];
                const month = parts[1];
                
                fetch(`/dashboard/stacked-chart-data?year=${year}&month=${month}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.labels.length === 0) {
                        stackedCtx.style.display = 'none';
                        stackedEmptyState.style.display = 'block';
                        if (categoryChart) {
                            categoryChart.destroy();
                            categoryChart = null;
                        }
                        return;
                    }
                    
                    stackedCtx.style.display = 'block';
                    stackedEmptyState.style.display = 'none';

                    const chartConfig = {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: data.datasets.map((ds, index) => {
                                const colors = [
                                    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', 
                                    '#8b5cf6', '#ec4899', '#14b8a6', '#f97316',
                                    '#6366f1', '#84cc16', '#eab308', '#06b6d4',
                                    '#3f6212', '#9f1239', '#4338ca', '#be123c'
                                ];
                                ds.backgroundColor = colors[index % colors.length];
                                return ds;
                            })
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    stacked: true,
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        font: { family: 'Inter', size: 13 },
                                        padding: 7
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let value = context.raw;
                                            return context.dataset.label + ': ₹' + value.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                                        }
                                    }
                                }
                            }
                        }
                    };

                    if (categoryChart) {
                        categoryChart.data.labels = data.labels;
                        categoryChart.data.datasets = chartConfig.data.datasets;
                        categoryChart.update();
                    } else {
                        categoryChart = new Chart(stackedCtx, chartConfig);
                    }
                })
                .catch(err => console.error('Error fetching stacked chart data:', err));
            }

            stackedMonthYearInput.addEventListener('change', fetchStackedChartData);
            fetchStackedChartData();
        }

        // Line Chart Logic
        const lineCtx = document.getElementById('monthlyExpenseLineChart');
        if (lineCtx) {
            let lineChart = null;
            const lineYearInput = document.getElementById('lineChartYear');
            const lineEmptyState = document.getElementById('line-chart-empty-state');

            function fetchLineChartData() {
                const year = lineYearInput.value;
                if (!year) return;
                
                fetch(`/dashboard/line-chart-data?year=${year}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    const totalData = data.data.reduce((a, b) => a + b, 0);
                    if (totalData === 0) {
                        lineCtx.style.display = 'none';
                        lineEmptyState.style.display = 'block';
                        if (lineChart) {
                            lineChart.destroy();
                            lineChart = null;
                        }
                        return;
                    }
                    
                    lineCtx.style.display = 'block';
                    lineEmptyState.style.display = 'none';

                    const chartConfig = {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Monthly Expenses',
                                data: data.data,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderWidth: 2,
                                pointBackgroundColor: '#3b82f6',
                                pointBorderColor: '#ffffff',
                                pointHoverBackgroundColor: '#ffffff',
                                pointHoverBorderColor: '#3b82f6',
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let value = context.raw;
                                            return ' ₹' + value.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                                        }
                                    }
                                }
                            }
                        }
                    };

                    if (lineChart) {
                        lineChart.data.labels = data.labels;
                        lineChart.data.datasets[0].data = data.data;
                        lineChart.update();
                    } else {
                        lineChart = new Chart(lineCtx, chartConfig);
                    }
                })
                .catch(err => console.error('Error fetching line chart data:', err));
            }

            lineYearInput.addEventListener('change', fetchLineChartData);
            fetchLineChartData();
        }
    });
</script>
