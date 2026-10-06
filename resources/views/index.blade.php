@extends('layout.app')

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="mb-2 text-primary f-w-700">Business Dashboard</h4>
            <p class="text-muted f-s-13">Welcome back! Here's what's happening with your store today.</p>
        </div>
    </div>

    <!-- Top Balances Row -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm bg-primary-light">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted text-uppercase f-w-600 f-s-12 mb-1">Business Balance</p>
                        <h3 class="mb-0 text-primary f-w-800">{{ number_format($businessBalance, 2) }}</h3>
                    </div>
                    <div class="h-50 w-50 d-flex-center bg-primary rounded-circle text-white shadow-sm" style="width: 50px !important; height: 50px !important;">
                        <i class="ti ti-building-bank f-s-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm bg-success-light">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted text-uppercase f-w-600 f-s-12 mb-1">Customer Receivables</p>
                        <h3 class="mb-0 text-success f-w-800">{{ number_format($customerReceivables, 2) }}</h3>
                    </div>
                    <div class="h-50 w-50 d-flex-center bg-success rounded-circle text-white shadow-sm" style="width: 50px !important; height: 50px !important;">
                        <i class="ti ti-arrow-down-right f-s-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm bg-warning-light">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted text-uppercase f-w-600 f-s-12 mb-1">Supplier Payables</p>
                        <h3 class="mb-0 text-warning f-w-800">{{ number_format($supplierPayables, 2) }}</h3>
                    </div>
                    <div class="h-50 w-50 d-flex-center bg-warning rounded-circle text-white shadow-sm" style="width: 50px !important; height: 50px !important;">
                        <i class="ti ti-arrow-up-right f-s-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm bg-info-light">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted text-uppercase f-w-600 f-s-12 mb-1">Pending Orders</p>
                        <h3 class="mb-0 text-info f-w-800">{{ number_format($pendingOrders) }}</h3>
                    </div>
                    <div class="h-50 w-50 d-flex-center bg-info rounded-circle text-white shadow-sm" style="width: 50px !important; height: 50px !important;">
                        <i class="ti ti-truck-delivery f-s-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Month Row -->
    <div class="row">
        <div class="col-12 mb-3">
            <h5 class="text-muted f-w-600 mb-0">Current Month Profile ({{ date('F Y') }})</h5>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <p class="text-muted text-uppercase f-w-600 f-s-11 mb-1">Monthly Sales</p>
                    <h4 class="mb-0 text-dark f-w-700">{{ number_format($currentMonthSales, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <p class="text-muted text-uppercase f-w-600 f-s-11 mb-1">Monthly Purchases</p>
                    <h4 class="mb-0 text-dark f-w-700">{{ number_format($currentMonthPurchases, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <p class="text-muted text-uppercase f-w-600 f-s-11 mb-1">Monthly Expenses</p>
                    <h4 class="mb-0 text-dark f-w-700">{{ number_format($currentMonthExpenses, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 {{ $currentMonthProfit >= 0 ? 'border-success border-bottom border-3' : 'border-danger border-bottom border-3' }}">
                <div class="card-body p-3">
                    <p class="text-muted text-uppercase f-w-600 f-s-11 mb-1">Monthly Net Profit</p>
                    <h4 class="mb-0 {{ $currentMonthProfit >= 0 ? 'text-success' : 'text-danger' }} f-w-800">{{ number_format($currentMonthProfit, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Top Products Row -->
    <div class="row">
        <!-- Monthly Trend Chart -->
        <div class="col-xl-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white pt-4 pb-0 border-0">
                    <h6 class="mb-0 f-w-700 text-dark">6-Month Trend (Sales, Expenses, Profit)</h6>
                </div>
                <div class="card-body p-3">
                    <div id="monthlyTrendChart" class="w-100"></div>
                </div>
            </div>
        </div>

        <!-- Top 10 Products -->
        <div class="col-xl-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white pt-4 pb-2 border-0">
                    <h6 class="mb-0 f-w-700 text-dark">Top 10 Products (This Month)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                        <table class="table table-hover table-borderless align-middle mb-0">
                            <thead class="table-light position-sticky top-0 shadow-sm" style="z-index: 1;">
                                <tr>
                                    <th class="py-2 f-s-12 text-muted">Product</th>
                                    <th class="py-2 f-s-12 text-muted text-center">Qty</th>
                                    <th class="py-2 f-s-12 text-muted text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $tp)
                                    <tr class="border-bottom border-light">
                                        <td class="py-2 f-w-600 f-s-13 text-dark">{{ $tp->product->name ?? 'Unknown' }}</td>
                                        <td class="py-2 text-center f-s-13">{{ number_format($tp->total_qty) }}</td>
                                        <td class="py-2 text-end f-w-600 text-success f-s-13">{{ number_format($tp->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-muted f-s-13">No sales yet this month.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Lists Row -->
    <div class="row">
        <!-- Recent Activities -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white pt-4 pb-2 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 f-w-700 text-dark">Recent Activities</h6>
                    <a href="{{ route('reportActivity') }}" class="btn btn-sm btn-light text-primary f-s-12">View All</a>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-3">
                        @forelse($recentActivities as $act)
                            <div class="d-flex align-items-center p-3 rounded bg-light border-start border-{{ $act['color'] }} border-3 shadow-sm">
                                <div class="me-3">
                                    <span class="badge bg-{{ $act['color'] }} rounded-pill p-2" style="width: 70px;">{{ $act['type'] }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 f-s-13 f-w-600">{{ $act['desc'] }}</h6>
                                    <p class="mb-0 text-muted f-s-11">{{ \Carbon\Carbon::parse($act['date'])->diffForHumans() }}</p>
                                </div>
                                <div class="ms-auto text-end">
                                    <h6 class="mb-0 f-w-700 text-{{ $act['color'] }}">{{ number_format($act['amount'], 2) }}</h6>
                                </div>
                            </div>
                        @empty
                            <div class="text-center p-4 text-muted f-s-13">No recent activities.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white pt-4 pb-2 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 f-w-700 text-dark">Recent Expenses</h6>
                    <a href="{{ route('reportExpense') }}" class="btn btn-sm btn-light text-primary f-s-12">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3 px-4 f-s-12 text-muted">Date</th>
                                    <th class="py-3 f-s-12 text-muted">Category</th>
                                    <th class="py-3 px-4 f-s-12 text-muted text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentExpenses as $exp)
                                    <tr>
                                        <td class="py-3 px-4 f-s-13">
                                            <span class="f-w-600 text-dark">{{ \Carbon\Carbon::parse($exp->date)->format('d M') }}</span>
                                            <span class="text-muted ms-1 f-s-11">{{ \Carbon\Carbon::parse($exp->created_at)->format('H:i') }}</span>
                                        </td>
                                        <td class="py-3 f-s-13 text-dark">
                                            <i class="ti ti-wallet text-danger me-2"></i>
                                            {{ $exp->category->name ?? 'Misc' }}
                                        </td>
                                        <td class="py-3 px-4 text-end f-w-700 text-danger f-s-13">
                                            {{ number_format($exp->amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-muted f-s-13">No recent expenses.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-css')
<style>
    .bg-primary-light { background-color: rgba(13, 110, 253, 0.08) !important; }
    .bg-success-light { background-color: rgba(40, 167, 69, 0.08) !important; }
    .bg-warning-light { background-color: rgba(255, 193, 7, 0.08) !important; }
    .bg-danger-light { background-color: rgba(220, 53, 69, 0.08) !important; }
    .bg-info-light { background-color: rgba(13, 202, 240, 0.08) !important; }
</style>
@endsection

@section('page-js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var options = {
            series: [{
                name: 'Sales',
                data: {!! json_encode($salesData) !!}
            }, {
                name: 'Expenses',
                data: {!! json_encode($expensesData) !!}
            }, {
                name: 'Net Profit',
                data: {!! json_encode($profitData) !!}
            }],
            chart: {
                height: 380,
                type: 'area',
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            colors: ['#28a745', '#dc3545', '#0d6efd'],
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: [2, 2, 3],
                dashArray: [0, 0, 0]
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: {!! json_encode($months) !!},
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#a1aab2' } }
            },
            yaxis: {
                labels: {
                    style: { colors: '#a1aab2' },
                    formatter: function (value) {
                        if(value >= 1000) return (value / 1000).toFixed(1) + "k";
                        return value;
                    }
                }
            },
            grid: {
                borderColor: 'rgba(0,0,0,0.05)',
                strokeDashArray: 4,
            },
            tooltip: {
                theme: 'light',
                y: { formatter: function (val) { return val.toFixed(2); } }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            }
        };

        var chart = new ApexCharts(document.querySelector("#monthlyTrendChart"), options);
        chart.render();
    });
</script>
@endsection
