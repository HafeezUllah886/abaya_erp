@extends('layout.app')
@section('content')
    <div class="container invoice-container">
        <!-- Header -->
        <div class="card mb-3">
            <div class="card-body p-4 pb-0">
                <div class="row align-items-center mb-4">
                    <div class="col-sm-6">
                        <div class="mb-2">
                            <h4 class="text-primary mb-1 f-w-700">{{ projectName() }}</h4>
                            <address class="text-muted mb-0">
                                {{ addressLineOne() }}<br>
                                {{ addressLineTwo() }}
                            </address>
                        </div>
                    </div>
                    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                        <div class="mb-2">
                            <h5 class="text-primary f-w-700 mb-2">Comprehensive Profit / Loss Report</h5>
                            <p class="mb-1 text-dark-800">From: <strong class="text-dark">{{ date('d M Y', strtotime($from)) }}</strong></p>
                            <p class="mb-1 text-dark-800">To: <strong class="text-dark">{{ date('d M Y', strtotime($to)) }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. Overall Performance -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="m-0 text-uppercase f-w-700 letter-spacing-1">1. Overall Financial Performance</h6>
            </div>
            <div class="card-body p-4">
                <div class="row align-items-start mb-3 py-2 text-center">
                    <div class="col-sm-3 border-end">
                        <p class="text-muted f-s-12 text-uppercase f-w-600 mb-1 letter-spacing-1">Total Sales</p>
                        <h4 class="text-success f-w-700 mb-1">{{ number_format($sales, 2) }}</h4>
                    </div>
                    <div class="col-sm-3 border-end">
                        <p class="text-muted f-s-12 text-uppercase f-w-600 mb-1 letter-spacing-1">Total Purchases</p>
                        <h4 class="text-warning f-w-700 mb-1">{{ number_format($purchases, 2) }}</h4>
                    </div>
                    <div class="col-sm-3 border-end">
                        <p class="text-muted f-s-12 text-uppercase f-w-600 mb-1 letter-spacing-1">Total Expenses</p>
                        <h4 class="text-danger f-w-700 mb-1">{{ number_format($expenses, 2) }}</h4>
                    </div>
                    <div class="col-sm-3">
                        <p class="text-muted f-s-13 text-uppercase f-w-700 mb-1 letter-spacing-1">Net Profit / Loss</p>
                        <h3 class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }} f-w-800 mb-1">{{ number_format($profit, 2) }}</h3>
                    </div>
                </div>
                
                <hr class="my-4 opacity-20">
                <h6 class="mb-3 text-uppercase text-muted f-w-600 letter-spacing-1 text-center">Trend Analysis</h6>
                <div id="trendChart" class="w-100"></div>
            </div>
        </div>

        <!-- 2. Revenue Details (Sales) -->
        <div class="row">
            <div class="col-md-7">
                <div class="card mb-3 h-100">
                    <div class="card-header bg-light">
                        <h6 class="m-0 text-uppercase f-w-700 letter-spacing-1">2. Products Sold Detail</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-striped table-hover text-center table-nowrap align-middle mb-0">
                                <thead class="table-light position-sticky top-0 shadow-sm">
                                    <tr>
                                        <th scope="col" class="m-0 p-2 text-start">Product</th>
                                        <th scope="col" class="m-0 p-2">Qty Sold</th>
                                        <th scope="col" class="m-0 p-2 text-end">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($soldProducts as $sp)
                                        <tr>
                                            <td class="text-start p-2">{{ $sp->product->name ?? 'N/A' }}</td>
                                            <td class="p-2">{{ number_format($sp->total_qty, 2) }}</td>
                                            <td class="text-end p-2 text-success f-w-600">{{ number_format($sp->total_amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-muted p-3">No sales recorded in this period.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card mb-3 h-100">
                    <div class="card-header bg-light">
                        <h6 class="m-0 text-uppercase f-w-700 letter-spacing-1">Top Selling Products</h6>
                    </div>
                    <div class="card-body p-4 d-flex-center">
                        <div id="topSoldChart" class="w-100"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Cost Details (Purchases & Expenses) -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card mb-3 h-100">
                    <div class="card-header bg-light">
                        <h6 class="m-0 text-uppercase f-w-700 letter-spacing-1">3. Products Purchased Detail</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-striped table-hover text-center table-nowrap align-middle mb-0">
                                <thead class="table-light position-sticky top-0 shadow-sm">
                                    <tr>
                                        <th scope="col" class="m-0 p-2 text-start">Product</th>
                                        <th scope="col" class="m-0 p-2">Qty Purchased</th>
                                        <th scope="col" class="m-0 p-2 text-end">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($purchasedProducts as $pp)
                                        <tr>
                                            <td class="text-start p-2">{{ $pp->product->name ?? 'N/A' }}</td>
                                            <td class="p-2">{{ number_format($pp->total_qty, 2) }}</td>
                                            <td class="text-end p-2 text-warning f-w-600">{{ number_format($pp->total_amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-muted p-3">No purchases recorded in this period.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-3 h-100">
                    <div class="card-header bg-light">
                        <h6 class="m-0 text-uppercase f-w-700 letter-spacing-1">4. Expenses by Category</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-sm-6">
                                <div id="expensePieChart" class="w-100"></div>
                            </div>
                            <div class="col-sm-6">
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless text-center align-middle mb-0">
                                        <tbody>
                                            @forelse ($expenseByCategory as $ec)
                                                <tr class="border-bottom">
                                                    <td class="text-start p-1 f-s-13">{{ $ec->category->name ?? 'N/A' }}</td>
                                                    <td class="text-end p-1 text-danger f-w-600 f-s-13">{{ number_format($ec->total_amount, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="2" class="text-muted p-2">No expenses recorded.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-footer float-end mb-4 mt-2">
            <button class="btn btn-primary m-1" onclick="window.print()" type="button">
                <i class="ti ti-printer"></i> Print Report
            </button>
        </div>
    </div>
@endsection

@section('page-css')
    <style>
        .letter-spacing-1 { letter-spacing: 0.06em; }
        
        @media print {
            body { background-color: #fff !important; color: #000 !important; }
            .app-navbar, .header-main, .footer-container, .go-top, .invoice-footer, #theme-customizer, .theme-customizer-container { display: none !important; }
            .app-content { margin-left: 0 !important; padding: 0 !important; margin-top: 0 !important; }
            .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; break-inside: avoid; margin-bottom: 20px !important;}
            .card-header { background-color: #f8f9fa !important; border-bottom: 1px solid #dee2e6 !important; }
            .container { max-width: 100% !important; width: 100% !important; padding: 0 !important; margin: 0 !important; }
            .table-responsive { overflow: visible !important; max-height: none !important; }
            /* Make charts print better */
            .apexcharts-canvas { max-width: 100% !important; }
        }
    </style>
@endsection

@section('page-js')
<script>
    // Trend Chart (Area)
    var trendOptions = {
        series: [{ name: 'Sales', data: {!! json_encode($salesChart) !!} }, 
                 { name: 'Purchases', data: {!! json_encode($purchasesChart) !!} }, 
                 { name: 'Expenses', data: {!! json_encode($expensesChart) !!} }, 
                 { name: 'Profit', data: {!! json_encode($profitChart) !!} }],
        chart: { height: 320, type: 'area', toolbar: { show: false } },
        colors: ['#28a745', '#ffc107', '#dc3545', '#0d6efd'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: { categories: {!! json_encode($dates) !!} },
        tooltip: { y: { formatter: function (val) { return val.toFixed(2) } } },
        legend: { position: 'top', horizontalAlign: 'right' }
    };
    var trendChart = new ApexCharts(document.querySelector("#trendChart"), trendOptions);
    trendChart.render();

    // Top Sold Products Chart (Bar)
    var topSoldOptions = {
        series: [{ name: 'Total Amount', data: {!! json_encode($topSoldProductsAmounts) !!} }],
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        colors: ['#28a745'],
        plotOptions: { bar: { horizontal: true, borderRadius: 4, dataLabels: { position: 'top' } } },
        dataLabels: {
            enabled: true,
            offsetX: 25,
            style: { fontSize: '11px', colors: ['#304758'] },
            formatter: function (val) { return val.toFixed(0); }
        },
        stroke: { show: true, width: 1, colors: ['#fff'] },
        xaxis: { categories: {!! json_encode($topSoldProductsNames) !!}, labels: { show: false } },
    };
    var topSoldChart = new ApexCharts(document.querySelector("#topSoldChart"), topSoldOptions);
    topSoldChart.render();

    // Expense Categories Chart (Donut)
    var expensePieOptions = {
        series: {!! json_encode($expenseCategoryAmounts) !!},
        chart: { type: 'donut', height: 300 },
        labels: {!! json_encode($expenseCategoryNames) !!},
        dataLabels: { enabled: false },
        legend: { position: 'bottom' },
        tooltip: { y: { formatter: function (val) { return val.toFixed(2) } } },
        plotOptions: { pie: { donut: { size: '65%' } } }
    };
    var expensePieChart = new ApexCharts(document.querySelector("#expensePieChart"), expensePieOptions);
    expensePieChart.render();
</script>
@endsection
