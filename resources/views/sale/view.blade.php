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
                                <img src="{{ asset('assets/images/IMG_20261011_013942.jpg') }}" alt="Header Image"
                                    class="img-fluid w-100">
                            </div>
                        </div>

                        <!-- Invoice Details -->

                        <div class="row align-items-start mb-3">
                            <div class="col-sm-4 mt-3 mt-sm-0">

                                <p class="text-dark f-s-12 text-uppercase f-w-700 mb-2 letter-spacing-1">
                                    SOLD TO / <span class="arabic-text">البيع إلى</span>
                                </p>

                                <div class="f-s-13 mb-0">
                                    @php
                                        $custTitle = $sale->customer_name ?: $sale->customer->title;
                                    @endphp
                                    <span class="text-dark f-w-600">
                                        {{ $custTitle }}
                                        @if (strtolower(trim($custTitle)) == 'daily sale')
                                            / <span class="arabic-text">بيع يومي</span>
                                        @endif
                                    </span>
                                    @if ($sale->customer->address)
                                        <div class="text-muted f-s-12">{{ $sale->customer->address }}</div>
                                    @endif
                                    @if ($sale->contact ?: $sale->customer->contact)
                                        <div class="text-muted f-s-12">{{ $sale->contact ?: $sale->customer->contact }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-4">

                            </div>

                            <div class="col-sm-4">
                                <p class="text-dark f-s-12 text-uppercase f-w-700 mb-2 letter-spacing-1">
                                    RECEIPT DETAILS / <span class="arabic-text">تفاصيل الإيصال</span>
                                </p>
                                <div class="d-flex justify-content-between mb-1 f-s-13">
                                    <span class="text-dark">Bill No. / <span class="arabic-text">رقم
                                            الفاتورة</span></span>
                                    <strong class="text-dark">#{{ $sale->bill_number ?? $sale->id }}</strong>
                                </div>
                                {{--  <div class="d-flex justify-content-between mb-1 f-s-13">
                                    <span class="text-dark">Receipt No. / <span class="arabic-text">رقم
                                            الإيصال</span></span>
                                    <strong class="text-dark">#{{ $sale->id }}</strong>
                                </div> --}}
                                <div class="d-flex justify-content-between mb-1 f-s-13">
                                    <span class="text-dark">Date / <span class="arabic-text">التاريخ</span></span>
                                    <strong class="text-dark">{{ date('d M Y', strtotime($sale->date)) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-0 f-s-13">
                                    <span class="text-dark">Delivery / <span class="arabic-text">التسليم</span></span>
                                    <strong
                                        class="text-dark">{{ date('d M Y', strtotime($sale->delivery_date ?? $sale->date)) }}</strong>
                                </div>
                            </div>



                        </div>

                        <!-- Divider -->
                        <hr class="my-2">

                        <!-- Items Table -->
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 50px;">No<br><span class="arabic-text">الرقم</span>
                                        </th>
                                        <th scope="col" style="width: 250px;">Product<br><span
                                                class="arabic-text">المنتج</span></th>
                                        <th scope="col" style="width: 120px;">Model<br><span
                                                class="arabic-text">الموديل</span></th>
                                        <th scope="col" class="text-end" style="width: 130px;">Price<br><span
                                                class="arabic-text">السعر</span></th>
                                        <th scope="col" class="text-end" style="width: 110px;">Quantity<br><span
                                                class="arabic-text">الكمية</span></th>
                                        <th scope="col" class="text-end" style="width: 110px;">Delivered<br><span
                                                class="arabic-text">تم التسليم</span></th>
                                        <th scope="col" class="text-center" style="width: 90px;">Unit<br><span
                                                class="arabic-text">الوحدة</span></th>
                                        <th scope="col" class="text-end" style="width: 140px;">Total<br><span
                                                class="arabic-text">الإجمالي</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sale->details as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="f-w-600 text-dark">{{ $item->product->name }}</td>
                                            <td>{{ $item->model ?? '-' }}</td>
                                            <td class="text-end">{{ number_format($item->price, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->qty, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->delivered_qty, 2) }}</td>
                                            <td class="text-center"><span
                                                    class="badge bg-light text-dark">{{ $item->product->unit }}</span></td>
                                            <td class="text-end text-dark">{{ number_format($item->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="text-dark">
                                        <td colspan="7" class="text-end border-top p-1">Sub Total / <span
                                                class="arabic-text">المجموع الفرعي</span></td>
                                        <td class="text-end text-dark border-top f-s-14 p-1">
                                            {{ number_format($sale->details->sum('amount'), 2) }}
                                        </td>
                                    </tr>
                                    <tr class="text-dark">
                                        <td colspan="7" class="text-end border-top p-1">
                                            VAT ({{ $sale->vat ?? 0 }}%) <span class="arabic-text">ضريبة القيمة المضافة

                                        </td>
                                        <td class="text-end text-dark border-top f-s-14 p-1">
                                            {{ number_format($sale->vat_amount ?? 0, 2) }}
                                        </td>
                                    </tr>
                                    <tr class="table-light f-w-700 text-dark">
                                        <td colspan="7" class="text-end border-top p-1">Grand Total <span
                                                class="arabic-text">الإجمالي الكلي</span></td>
                                        <td class="text-end text-primary f-w-700 border-top f-s-16 p-1">
                                            {{ number_format($sale->total_bill, 2) }}
                                        </td>
                                    </tr>
                                    <tr class="text-dark">
                                        <td colspan="7" class="text-end border-top p-1">Total Paid / <span
                                                class="arabic-text">إجمالي المدفوع</span></td>
                                        <td class="text-end text-success f-w-700 border-top f-s-14 p-1">
                                            {{ number_format($sale->payments->sum('amount'), 2) }}
                                        </td>
                                    </tr>
                                    <tr class="table-light f-w-700 text-dark">
                                        <td colspan="7" class="text-end border-top p-1">Balance / <span
                                                class="arabic-text">الرصيد المتبقي</span></td>
                                        <td class="text-end text-danger f-w-700 border-top f-s-16 p-1">
                                            {{ number_format($sale->total_bill - $sale->payments->sum('amount'), 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer p-4 bg-white border-top-0">
                        @if ($sale->notes)
                            <div class="row">
                                <div class="col-12 text-end">
                                    <strong class="text-dark f-s-14">Notes / <span
                                            class="arabic-text">ملاحظات</span></strong>
                                    @if ($sale->notes)
                                        <div class="text-muted mt-1">{{ $sale->notes }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="f-w-700 mb-3 text-dark">TERMS AND CONDITION / <span class="arabic-text">الشروط
                                        والأحكام</span></h6>
                                <ul class="text-dark f-s-13 ps-3 mb-0" style="list-style-type: disc;">
                                    <li class="mb-2">
                                        <div>Down payment of 50% on each abaya.</div>
                                        <div class="arabic-text text-muted">تُدفع دفعة مقدمة بنسبة 50% عن كل عباية.</div>
                                    </li>
                                    <li class="mb-2">
                                        <div>Down payment is not refundable after 24 hours.</div>
                                        <div class="arabic-text text-muted">الدفعة المقدمة غير قابلة للاسترداد بعد 24 ساعة.
                                        </div>
                                    </li>
                                    <li class="mb-2">
                                        <div>Alterations are only valid within one week of delivery date.</div>
                                        <div class="arabic-text text-muted">التعديلات متاحة خلال أسبوع واحد فقط من تاريخ
                                            التسليم.</div>
                                    </li>
                                    <li class="mb-2">
                                        <div>Abaya will be sold if clients fail to collect their order within 10 weeks.
                                        </div>
                                        <div class="arabic-text text-muted">يحق بيع العبايات إذا لم يستلم العميل طلبه خلال
                                            10 أسابيع.</div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="invoice-footer float-end mb-3 mt-3">
                    <button class="btn btn-primary m-1" onclick="window.print()" type="button">
                        <i class="ti ti-printer"></i> Print Receipt / <span class="arabic-text">طباعة الإيصال</span>
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
