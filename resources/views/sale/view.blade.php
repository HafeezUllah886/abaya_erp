@extends('layout.app')
@section('content')
    <div class="container invoice-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-4">
                        <!-- Invoice Header Image -->
                        <div class="row mb-4">
                            <div class="col-12 text-center">
                                <img src="{{ asset('assets/images/IMG_20261011_013942.jpg') }}" alt="Header Image" class="img-fluid w-100">
                            </div>
                        </div>

                        <!-- Invoice Details -->
                        <div class="row align-items-start mb-3">
                            <div class="col-sm-4">
                                <p class="text-muted f-s-11 text-uppercase f-w-600 mb-1 letter-spacing-1">Receipt Details</p>
                                <h6 class="text-dark f-w-700 mb-1">Receipt No. #{{ $sale->id }}</h6>
                                <p class="mb-0 text-muted f-s-13">
                                    Date: <strong>{{ date('d M Y', strtotime($sale->date)) }}</strong><br>
                                    Delivery: <strong>{{ date('d M Y', strtotime($sale->delivery_date ?? $sale->date)) }}</strong>
                                </p>
                            </div>
                            <div class="col-sm-4 mt-3 mt-sm-0">
                                <p class="text-muted f-s-11 text-uppercase f-w-600 mb-1 letter-spacing-1">Sale Type / Sold To</p>
                                <h6 class="text-dark f-w-700 mb-1">
                                    <span class="badge bg-primary me-1">{{ strtoupper($sale->sale_type) }}</span>
                                    {{ $sale->customer_name ?: $sale->customer->title }}
                                </h6>
                                <address class="mb-0 text-muted f-s-13">
                                    {{ $sale->customer->address }} <br>
                                    {{ $sale->contact ?: $sale->customer->contact }}
                                </address>
                            </div>
                            <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                                <p class="text-muted f-s-11 text-uppercase f-w-600 mb-1 letter-spacing-1">Payment Info</p>
                                @if ($sale->payments->count() > 0)
                                    @foreach ($sale->payments as $payment)
                                        <p class="mb-1 f-s-13 text-dark-800">{{ number_format($payment->amount, 2) }}
                                            <strong class="badge bg-success-light text-success ms-1">{{ $payment->account->title }}</strong>
                                            <br><span class="text-muted">{{ $payment->notes }}</span>
                                        </p>
                                    @endforeach
                                @else
                                    <p class="mb-1 f-s-13 text-dark-800">Unpaid</p>
                                @endif
                            </div>
                        </div>

                        <!-- Divider -->
                        <hr class="my-2">

                        <!-- Items Table -->
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 60px;">No</th>
                                        <th scope="col" style="width: 300px;">Product</th>
                                        <th scope="col" class="text-end" style="width: 150px;">Price</th>
                                        <th scope="col" class="text-end" style="width: 150px;">Quantity</th>
                                        <th scope="col" class="text-end" style="width: 150px;">Delivered</th>
                                        <th scope="col" style="width: 100px;">Unit</th>
                                        <th scope="col" class="text-end" style="width: 180px;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sale->details as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="f-w-600 text-dark">{{ $item->product->name }}</td>
                                            <td class="text-end">{{ number_format($item->price, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->qty, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->delivered_qty, 2) }}</td>
                                            <td><span class="badge bg-light text-dark">{{ $item->product->unit }}</span>
                                            </td>
                                            <td class="text-end text-dark">{{ number_format($item->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="text-dark">
                                        <td colspan="6" class="text-end border-top">Sub Total</td>
                                        <td class="text-end text-dark border-top f-s-14">
                                            {{ number_format($sale->details->sum('amount'), 2) }}</td>
                                    </tr>
                                    <tr class="text-dark">
                                        <td colspan="6" class="text-end border-top">VAT ({{ $sale->vat ?? 0 }}%)</td>
                                        <td class="text-end text-dark border-top f-s-14">
                                            {{ number_format($sale->vat_amount ?? 0, 2) }}</td>
                                    </tr>
                                    <tr class="table-light f-w-700 text-dark">
                                        <td colspan="6" class="text-end border-top">Grand Total</td>
                                        <td class="text-end text-primary f-w-700 border-top f-s-16">
                                            {{ number_format($sale->total_bill, 2) }}</td>
                                    </tr>
                                    <tr class="text-dark">
                                        <td colspan="6" class="text-end border-top">Total Paid</td>
                                        <td class="text-end text-success f-w-700 border-top f-s-14">
                                            {{ number_format($sale->payments->sum('amount'), 2) }}</td>
                                    </tr>
                                    <tr class="table-light f-w-700 text-dark">
                                        <td colspan="6" class="text-end border-top">Balance</td>
                                        <td class="text-end text-danger f-w-700 border-top f-s-16">
                                            {{ number_format($sale->total_bill - $sale->payments->sum('amount'), 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer p-4">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6"></div>
                            <div class="col-lg-6 col-md-6 col-sm-6 text-end"><strong class="text-dark">Notes</strong>
                                {{ $sale->notes }}
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6 class="f-w-700 mb-2">TERMS AND CONDITION</h6>
                                <ul class="text-muted f-s-12 ps-3 mb-0" style="list-style-type: disc;">
                                    <li>Down payment of 50% on each abaya.</li>
                                    <li>Down payment is not refundable after 24 hours.</li>
                                    <li>Alterations are only valid within one week of delivery date.</li>
                                    <li>Abaya will be sold if clients fail to collect their order within 10 weeks</li>
                                </ul>
                            </div>
                            <div class="col-md-6 text-end" dir="rtl">
                                <h6 class="f-w-700 mb-2 arabic-text">الشروط والأحكام</h6>
                                <ul class="text-muted f-s-12 pe-3 mb-0 arabic-text" style="list-style-type: disc;">
                                    <li>دفعة مقدمة بنسبة 50% على كل عباية.</li>
                                    <li>الدفعة المقدمة غير مستردة بعد 24 ساعة.</li>
                                    <li>التعديلات صالحة فقط في غضون أسبوع من تاريخ التسليم.</li>
                                    <li>سيتم بيع العباية إذا فشل العملاء في استلام طلبهم خلال 10 أسابيع.</li>
                                    <li>أخذ الصور لن يسمح به.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="invoice-footer float-end mb-3 mt-3">
                    <button class="btn btn-primary m-1" onclick="window.print()" type="button">
                        <i class="ti ti-printer"></i> Print Receipt
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('page-css')
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        .arabic-text {
            font-family: 'Cairo', sans-serif;
        }

        .letter-spacing-1 {
            letter-spacing: 0.06em;
        }

        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.12);
        }

        body.dark .bg-success-light {
            background-color: rgba(40, 167, 69, 0.2);
        }

        body.dark .table-light {
            --bs-table-bg: #2c2f38;
            --bs-table-color: #c8ccd6;
            border-color: #3d4251;
        }

        @page {
            size: A4;
            margin: 15mm;
        }

        @media print {
            body {
                background-color: #fff !important;
                color: #000 !important;
            }

            .app-navbar,
            .header-main,
            .footer-container,
            .go-top,
            .invoice-footer,
            #theme-customizer,
            .theme-customizer-container {
                display: none !important;
            }

            .app-content {
                margin-left: 0 !important;
                padding: 0 !important;
                margin-top: 0 !important;
            }

            .card {
                border: 0 !important;
                box-shadow: none !important;
            }

            .card-body {
                padding: 0 !important;
            }

            .container {
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Fix table overflow on print */
            .table-responsive {
                overflow: visible !important;
            }

            .table-responsive table {
                width: 100% !important;
                table-layout: auto !important;
            }

            .table-responsive th,
            .table-responsive td {
                width: auto !important;
            }
        }
    </style>
@endsection

@section('page-js')
@endsection
