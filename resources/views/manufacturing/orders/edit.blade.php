@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Manufacturing Order #{{ $order->id }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('manufacturing_orders.update', $order->id) }}" method="POST">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label>Date</label>
                                    <input type="date" name="date" class="form-control" value="{{ $order->date }}"
                                        required>
                                </div>
                                <div class="col-md-3">
                                    <label>Tailor</label>
                                    <select name="tailor_id" class="form-control" required>
                                        <option value="">Select Tailor</option>
                                        @foreach ($tailors as $t)
                                            <option value="{{ $t->id }}"
                                                {{ $order->tailor_id == $t->id ? 'selected' : '' }}>{{ $t->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Payment Type</label>
                                    <select name="payment_type" id="payment_type" class="form-control" required>
                                        <option value="Unpaid" {{ $order->payment_type == 'Unpaid' ? 'selected' : '' }}>
                                            Unpaid</option>
                                        <option value="Paid" {{ $order->payment_type == 'Paid' ? 'selected' : '' }}>Paid
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3" id="business_account_div"
                                    style="display:{{ $order->payment_type == 'Paid' ? 'block' : 'none' }};">
                                    <label>Business Account</label>
                                    <select name="business_account_id" class="form-control">
                                        <option value="">Select Account</option>
                                        @foreach ($business_accounts as $ba)
                                            <option value="{{ $ba->id }}"
                                                {{ $order->business_account_id == $ba->id ? 'selected' : '' }}>
                                                {{ $ba->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr>
                            <h5>Finished Abayas (Ordered Products)</h5>
                            <table class="table table-bordered" id="products_table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Order Qty</th>
                                        <th>Received Qty</th>
                                        <th><button type="button" class="btn btn-sm btn-success"
                                                id="add_product">+</button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($order->products->count() > 0)
                                        @foreach($order->products as $op)
                                            <tr>
                                                <td>
                                                    <select name="product_id[]" class="form-control">
                                                        <option value="">Select Product</option>
                                                        @foreach ($finished_products as $p)
                                                            <option value="{{ $p->id }}" {{ $op->product_id == $p->id ? 'selected' : '' }}>{{ $p->title ?? $p->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" name="order_qty[]" class="form-control" step="0.01" value="{{ $op->order_qty }}"></td>
                                                <td><input type="number" name="received_qty[]" class="form-control" step="0.01" value="{{ $op->received_qty }}"></td>
                                                <td><button type="button" class="btn btn-sm btn-danger remove_row">-</button></td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <select name="product_id[]" class="form-control">
                                                    <option value="">Select Product</option>
                                                    @foreach ($finished_products as $p)
                                                        <option value="{{ $p->id }}">{{ $p->title ?? $p->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" name="order_qty[]" class="form-control" step="0.01" value="0"></td>
                                            <td><input type="number" name="received_qty[]" class="form-control" step="0.01" value="0"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger remove_row">-</button></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <hr>
                            <h5>Raw Materials</h5>
                            <table class="table table-bordered" id="materials_table">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Own Qty</th>
                                        <th>Customer Qty</th>
                                        <th>Est. Cost Price (Unit)</th>
                                        <th>Total Cost</th>
                                        <th><button type="button" class="btn btn-sm btn-success"
                                                id="add_material">+</button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($order->materials->count() > 0)
                                        @foreach($order->materials as $om)
                                            <tr>
                                                <td>
                                                    <select name="material_id[]" class="form-control material_select">
                                                        <option value="">Select Material</option>
                                                        @foreach ($raw_materials as $rm)
                                                            <option value="{{ $rm->id }}" data-price="{{ avgPurchasePrice('all', 'all', $rm->id) }}" {{ $om->product_id == $rm->id ? 'selected' : '' }}>{{ $rm->title ?? $rm->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" name="own_qty[]" class="form-control own_qty" step="0.01" value="{{ $om->own_qty }}"></td>
                                                <td><input type="number" name="customer_qty[]" class="form-control" step="0.01" value="{{ $om->customer_qty }}"></td>
                                                <td><input type="number" name="cost_price[]" class="form-control cost_price" step="0.01" value="{{ $om->cost_price }}"></td>
                                                <td><input type="number" class="form-control total_cost" value="{{ number_format($om->own_qty * $om->cost_price, 2, '.', '') }}" readonly></td>
                                                <td><button type="button" class="btn btn-sm btn-danger remove_row">-</button></td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <select name="material_id[]" class="form-control material_select">
                                                    <option value="">Select Material</option>
                                                    @foreach ($raw_materials as $rm)
                                                        <option value="{{ $rm->id }}" data-price="{{ avgPurchasePrice('all', 'all', $rm->id) }}">{{ $rm->title ?? $rm->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" name="own_qty[]" class="form-control own_qty" step="0.01" value="0"></td>
                                            <td><input type="number" name="customer_qty[]" class="form-control" step="0.01" value="0"></td>
                                            <td><input type="number" name="cost_price[]" class="form-control cost_price" step="0.01"></td>
                                            <td><input type="number" class="form-control total_cost" readonly></td>
                                            <td><button type="button" class="btn btn-sm btn-danger remove_row">-</button></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Order Notes</label>
                                    <textarea name="notes" class="form-control" rows="3">{{ $order->notes }}</textarea>
                                </div>
                                <div class="col-md-3 offset-md-3 text-end">
                                    <h5>Total Estimated Cost: <span id="grand_total">0.00</span></h5>
                                    <label>Amount (Stitching Charges / Payment)</label>
                                    <input type="number" name="total_amount" class="form-control mb-3" step="0.01"
                                        value="{{ $order->total_amount }}" required>
                                    <button type="submit" class="btn btn-primary w-100">Update Order</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script>
        $(document).ready(function() {
            $('#payment_type').change(function() {
                if ($(this).val() == 'Paid') {
                    $('#business_account_div').show();
                } else {
                    $('#business_account_div').hide();
                }
            });

            // Add Product Row
            $('#add_product').click(function() {
                var row = `<tr>
                <td>
                    <select name="product_id[]" class="form-control">
                        <option value="">Select Product</option>
                        @foreach ($finished_products as $p)
                            <option value="{{ $p->id }}">{{ $p->title ?? $p->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="order_qty[]" class="form-control" step="0.01" value="0"></td>
                <td><input type="number" name="received_qty[]" class="form-control" step="0.01" value="0"></td>
                <td><button type="button" class="btn btn-sm btn-danger remove_row">-</button></td>
            </tr>`;
                $('#products_table tbody').append(row);
            });

            // Add Material Row
            $('#add_material').click(function() {
                var row = `<tr>
                <td>
                    <select name="material_id[]" class="form-control material_select">
                        <option value="">Select Material</option>
                        @foreach ($raw_materials as $rm)
                            <option value="{{ $rm->id }}" data-price="{{ avgPurchasePrice('all', 'all', $rm->id) }}">{{ $rm->title ?? $rm->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="own_qty[]" class="form-control own_qty" step="0.01" value="0"></td>
                <td><input type="number" name="customer_qty[]" class="form-control" step="0.01" value="0"></td>
                <td><input type="number" name="cost_price[]" class="form-control cost_price" step="0.01"></td>
                <td><input type="number" class="form-control total_cost" readonly></td>
                <td><button type="button" class="btn btn-sm btn-danger remove_row">-</button></td>
            </tr>`;
                $('#materials_table tbody').append(row);
            });

            // Remove Row
            $(document).on('click', '.remove_row', function() {
                $(this).closest('tr').remove();
                calculateTotal();
            });

            // Calculate cost on material or qty change
            $(document).on('change', '.material_select', function() {
                var price = $(this).find(':selected').data('price') || 0;
                $(this).closest('tr').find('.cost_price').val(price);
                calculateTotal();
            });

            $(document).on('input', '.own_qty, .cost_price', function() {
                calculateTotal();
            });

            function calculateTotal() {
                var grandTotal = 0;
                $('#materials_table tbody tr').each(function() {
                    var qty = parseFloat($(this).find('.own_qty').val()) || 0;
                    var price = parseFloat($(this).find('.cost_price').val()) || 0;
                    var total = qty * price;
                    $(this).find('.total_cost').val(total.toFixed(2));
                    grandTotal += total;
                });
                $('#grand_total').text(grandTotal.toFixed(2));
            }
            
            // Calculate initially
            calculateTotal();
        });
    </script>
@endsection
