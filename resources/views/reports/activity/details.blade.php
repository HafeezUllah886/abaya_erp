@extends('layout.app')
@section('content')
    <div class="container invoice-container">
        <div class="row">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-body p-4 pb-3">
                        <!-- Invoice Header -->
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
                                    <h5 class="text-primary f-w-700 mb-2">Comprehensive Activity Report</h5>
                                    <p class="mb-1 text-dark-800">From: <strong class="text-dark">{{ date('d M Y', strtotime($from)) }}</strong></p>
                                    <p class="mb-1 text-dark-800">To: <strong class="text-dark">{{ date('d M Y', strtotime($to)) }}</strong></p>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Tiles -->
                        @php
                            $totalSales = $sortedActivities->where('type', 'Sale')->sum('amount');
                            $totalPurchases = $sortedActivities->where('type', 'Purchase')->sum('amount');
                            $totalExpenses = $sortedActivities->where('type', 'Expense')->sum('amount');
                            $totalCount = $sortedActivities->count();
                        @endphp
                        <hr class="my-3 opacity-20">
                        <div class="row align-items-start mb-1 py-2 text-center">
                            <div class="col-sm-3 border-end">
                                <p class="text-muted f-s-12 text-uppercase f-w-600 mb-1 letter-spacing-1">Total Activities</p>
                                <h4 class="text-dark f-w-700 mb-1">{{ number_format($totalCount) }}</h4>
                            </div>
                            <div class="col-sm-3 border-end">
                                <p class="text-muted f-s-12 text-uppercase f-w-600 mb-1 letter-spacing-1">Sales Generated</p>
                                <h4 class="text-success f-w-700 mb-1">{{ number_format($totalSales, 2) }}</h4>
                            </div>
                            <div class="col-sm-3 border-end">
                                <p class="text-muted f-s-12 text-uppercase f-w-600 mb-1 letter-spacing-1">Purchases Logged</p>
                                <h4 class="text-warning f-w-700 mb-1">{{ number_format($totalPurchases, 2) }}</h4>
                            </div>
                            <div class="col-sm-3">
                                <p class="text-muted f-s-12 text-uppercase f-w-600 mb-1 letter-spacing-1">Expenses Logged</p>
                                <h4 class="text-danger f-w-700 mb-1">{{ number_format($totalExpenses, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="m-0 text-uppercase f-w-700 letter-spacing-1">Activity Log</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="p-3" style="width: 140px;">Date & Time</th>
                                        <th scope="col" class="p-3">Ref ID</th>
                                        <th scope="col" class="p-3">Type</th>
                                        <th scope="col" class="p-3">Description</th>
                                        <th scope="col" class="p-3 text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sortedActivities as $activity)
                                        <tr>
                                            <td class="p-3">
                                                <div class="f-w-600 text-dark">{{ date('d M Y', strtotime($activity['date'])) }}</div>
                                                <div class="f-s-12 text-muted">{{ date('h:i A', strtotime($activity['created_at'])) }}</div>
                                            </td>
                                            <td class="p-3 f-w-600">{{ $activity['ref'] ?? 'N/A' }}</td>
                                            <td class="p-3">
                                                <span class="badge bg-{{ $activity['color'] }}-light text-{{ $activity['color'] }} p-2 f-s-11">
                                                    {{ $activity['type'] }}
                                                </span>
                                            </td>
                                            <td class="p-3">
                                                {{ $activity['description'] }}
                                            </td>
                                            <td class="p-3 text-end f-w-700 text-{{ $activity['color'] }}">
                                                {{ $activity['amount'] > 0 ? number_format($activity['amount'], 2) : '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center p-4 text-muted">
                                                No activities found in this date range.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="invoice-footer float-end mb-4 mt-3">
                    <button class="btn btn-primary m-1" onclick="window.print()" type="button">
                        <i class="ti ti-printer"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-css')
    <style>
        .letter-spacing-1 { letter-spacing: 0.06em; }
        .bg-success-light { background-color: rgba(40, 167, 69, 0.12); }
        .bg-warning-light { background-color: rgba(255, 193, 7, 0.12); }
        .bg-danger-light { background-color: rgba(220, 53, 69, 0.12); }
        .bg-primary-light { background-color: rgba(13, 110, 253, 0.12); }
        .bg-info-light { background-color: rgba(13, 202, 240, 0.12); }
        .bg-secondary-light { background-color: rgba(108, 117, 125, 0.12); }
        
        @media print {
            body { background-color: #fff !important; color: #000 !important; }
            .app-navbar, .header-main, .footer-container, .go-top, .invoice-footer, #theme-customizer, .theme-customizer-container { display: none !important; }
            .app-content { margin-left: 0 !important; padding: 0 !important; margin-top: 0 !important; }
            .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; break-inside: avoid; margin-bottom: 20px !important;}
            .card-header { background-color: #f8f9fa !important; border-bottom: 1px solid #dee2e6 !important; }
            .container { max-width: 100% !important; width: 100% !important; padding: 0 !important; margin: 0 !important; }
            .badge { border: 1px solid #ccc; background-color: transparent !important; color: #000 !important; }
        }
    </style>
@endsection
