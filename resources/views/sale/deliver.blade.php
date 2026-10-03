@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card ">
                <div class="card-header d-flex justify-content-between">
                    <h5>Deliver & Receive Payment (Sale #{{ $sale->id }})</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('sale.deliver.store', $sale->id) }}" method="post" id="deliverForm">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3">Deliver Products</h6>
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <th width="40%">Item</th>
                                        <th class="text-center">Ordered Qty</th>
                                        <th class="text-center">Already Delivered</th>
                                        <th class="text-center">Remaining Qty</th>
                                        <th class="text-center">Deliver Now</th>
                                    </thead>
                                    <tbody id="products_list">
                                        @foreach ($sale->details as $item)
                                            @php
                                                $product_id = $item->product_id;
                                                $remaining = $item->qty - $item->delivered_qty;
                                            @endphp
                                            <tr>
                                                <td class="p-1">{{ $item->product->name }}</td>
                                                <td class="text-center p-1">{{ $item->qty }}</td>
                                                <td class="text-center p-1">{{ $item->delivered_qty }}</td>
                                                <td class="text-center p-1">{{ $remaining }}</td>
                                                <td class="p-0">
                                                    <input type="number" name="deliver_now[{{ $product_id }}]" 
                                                        min="0" max="{{ $remaining }}" step="any" value="{{ $remaining > 0 ? $remaining : 0 }}"
                                                        class="form-control form-control-sm text-center p-1">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-12 mt-4" id="accounts">
                                <h6 class="mb-3">Receive Balance</h6>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <strong>Total Bill: </strong> {{ number_format($sale->total_bill, 2) }}
                                    </div>
                                    <div class="col-6">
                                        <strong>Remaining Balance: </strong> {{ number_format($balance, 2) }}
                                    </div>
                                </div>
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <th>Account</th>
                                        <th class="text-center">Notes</th>
                                        <th class="text-center">Amount</th>
                                    </thead>
                                    <tbody id="accounts_list">
                                        @foreach ($accounts as $account)
                                            <input type="hidden" name="account_id[]" value="{{ $account->id }}">
                                            <tr>
                                                <td>{{ $account->title }}</td>
                                                <td><input type="text" name="payment_notes[]" class="form-control"></td>
                                                <td><input type="number" name="payment_amount[]"
                                                        id="paymnet_amount_{{ $account->id }}"
                                                        oninput="calculatePayment()" value="0"
                                                        class="form-control text-center"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-end">Total Receiving</th>
                                            <th class="text-center" id="totalPayment">0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label for="date">Delivery / Payment Date</label>
                                    <input type="date" name="date" id="date" required
                                        value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" cols="30" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary w-100">Submit Delivery & Payment</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script>
        function calculatePayment() {
            var total = 0;
            $("input[id^='paymnet_amount_']").each(function() {
                var inputValue = $(this).val();
                if (inputValue) {
                    total += parseFloat(inputValue);
                }
            });

            $("#totalPayment").html(total.toFixed(2));
        }

        $("#deliverForm").submit(function(e) {
            var total = parseFloat($("#totalPayment").text());
            var balance = {{ $balance }};
            if (total > balance) {
                e.preventDefault();
                alert("Total Receiving amount cannot be greater than the remaining balance.");
            }
        });
    </script>
@endsection
