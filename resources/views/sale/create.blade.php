@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card ">
                <div class="card-header d-flex justify-content-between">
                    <h5>Create Sale</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('sale.store') }}" method="post" id="saleForm">

                        @csrf
                        <div class="row">
                            <div class="col-3">
                                <select name="sale_type" id="sale_type" class="form-select mb-3">
                                    <option value="order">Order</option>
                                    <option value="RD">RD</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <table class="table table-bordered" id="products_table">
                                <thead>
                                    <tr>
                                        <th class="p-1">Product</th>
                                        <th class="text-center p-1">Price</th>
                                        <th class="text-center p-1">Qty</th>
                                        <th class="text-center p-1">Delivered</th>
                                        <th class="text-end p-1">Amount</th>
                                        <th class="text-center p-1"><button type="button" class="btn btn-sm btn-success"
                                                id="add_product">+</button></th>
                                    </tr>
                                </thead>
                                <tbody id="products_list">
                                    <tr>
                                        <td class="p-1">
                                            <select name="id[]" class="form-control">
                                                <option value="">Select Product</option>
                                                @foreach ($products as $p)
                                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td class="p-1"><input type="number" name="price[]" class="form-control price"
                                                step="0.01" value="0"></td>
                                        <td class="p-1"><input type="number" name="qty[]" class="form-control qty"
                                                step="0.01" value="0"></td>
                                        <td class="p-1"><input type="number" name="delivered_qty[]"
                                                class="form-control delivered_qty" step="0.01" value="0"></td>
                                        <td class="p-1"><input type="number" name="amount[]" class="form-control amount"
                                                step="0.01" value="0" readonly></td>
                                        <td class="p-1"><button type="button"
                                                class="btn btn-sm btn-danger remove_row">-</button></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" id="date" required value="{{ date('Y-m-d') }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="date">Delivery Date</label>
                                    <input type="date" name="delivery_date" id="delivery_date" required
                                        value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-group">
                                    <label for="vat">VAT (%)</label>
                                    <div class="input-group">
                                        <input type="number" name="vat" id="vat" oninput="updateTotal()"
                                            value="5" class="form-control">
                                        <input type="number" name="vat_amount" id="vat_amount" readonly
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="vat">Total Bill</label>
                                    <input type="number" name="total_bill" id="total_bill" readonly value="0"
                                        class="form-control">

                                </div>
                            </div>
                            <div class="col-3 mt-2">
                                <div class="form-group">
                                    <label for="customer">Customer</label>
                                    <select name="customer_id" id="customer_id" value="{{ $customers[0]->id }}" required
                                        class="select2 w-100">
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->title }}</option>
                                        @endforeach
                                    </select>
                                    <input type="customer_name" id="customer_name" class="form-control mt-2"
                                        placeholder="Enter Customer Name" name="customer_name">
                                </div>
                            </div>
                            <div class="col-3 mt-2">
                                <div class="form-group">
                                    <label for="contact">Contact Number</label>
                                    <input type="text" name="contact" id="contact" class="form-control">

                                </div>
                            </div>
                            <div class="col-12" id="accounts">
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

                                                <td><input type="text" name="payment_notes[]" class="form-control">
                                                </td>
                                                <td><input type="number" name="payment_amount[]"
                                                        id="paymnet_amount_{{ $account->id }}"
                                                        oninput="calculatePayment()" value="0"
                                                        class="form-control text-center"></td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-end">Total</th>
                                            <th class="text-center" id="totalPayment">0.00</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" cols="30" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary w-100">Create Sale</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Default Datatable end -->
    </div>
@endsection
@section('page-css')
    <link href="{{ asset('assets/vendor/select/select2.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('page-js')
    <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            $('#product').select2({
                placeholder: "Select a Product",
                allowClear: true,
                width: '100%'
            });

            checkCustomer();
            $("#customer_id").on('change', function() {
                checkCustomer();
            });

            $('#add_product').on('click', function() {
                var newRow = $('#products_table tbody tr:first').clone();
                newRow.find('input').val(0);
                newRow.find('select[name="id[]"]').val('');
                let type = $('#sale_type').val();
                if (type == 'RD') {
                    newRow.find('.delivered_qty').prop('readonly', true);
                } else {
                    newRow.find('.delivered_qty').prop('readonly', false);
                }
                $('#products_table tbody').append(newRow);
            });
        });

        function checkCustomer() {

            if ($("#customer_id").val() == 2) {
                $("#customer_name").show();

            } else {
                $("#customer_name").hide();
            }
        }

        $("#product").on('select2:select', function(e) {
            var value = e.params.data.id;
            if (value) {
                getSingleProduct(value);
                $(this).val(null).trigger('change');
                $(this).select2('open');
            }
        });

        var existingProducts = [];

        function getSingleProduct(id) {
            $.ajax({
                url: "{{ url('purchases/getproduct/') }}/" + id,
                method: "GET",
                success: function(product) {
                    let found = $.grep(existingProducts, function(element) {
                        return element === product.id;
                    });
                    if (found.length > 0) {

                    } else {

                        var id = product.id;
                        var html = '<tr id="row_' + id + '">';
                        html += '<td class="p-1">' + product.name + '</td>';

                        html += '<td class="p-0"><input type="number" name="price[]" step="any" value="' +
                            product.price +
                            '" min="0" class="form-control form-control-sm text-center p-1" id="price_' + id +
                            '" oninput="updateChanges(' +
                            id +
                            ')"></td>';
                        html +=
                            '<td class="p-0"><input type="number" name="qty[]" min="0.1" oninput="updateChanges(' +
                            id +
                            ')" min="0" step="any" value="0" class="form-control form-control-sm text-center p-1" id="qty_' +
                            id + '"></td>';
                        html +=
                            '<td class="p-0"><input type="number" name="delivered_qty[]" min="0" step="any" value="0" class="form-control form-control-sm text-center p-1" id="delivered_qty_' +
                            id + '"></td>';
                        html +=
                            '<td class="p-0"><input type="number" name="amount[]" min="0.1" readonly required step="any" value="1" class="form-control form-control-sm text-center p-1" id="amount_' +
                            id + '"></td>';
                        html += '<td class="p-0"> <span class="btn btn-sm btn-danger" onclick="deleteRow(' +
                            id + ')">X</span> </td>';
                        html += '<input type="hidden" name="id[]" value="' + id + '">';
                        html += '</tr>';
                        $("#products_list").prepend(html);
                        existingProducts.push(id);
                        updateChanges(id);
                    }
                }
            });
        }

        $('#sale_type').on('change', function() {
            let type = $(this).val();
            
            $('#products_table tbody tr').each(function() {
                let row = $(this);
                let qty = row.find('.qty').val();
                let deliveredQty = row.find('.delivered_qty');
                
                if (type == 'RD') {
                    deliveredQty.prop('readonly', true);
                    deliveredQty.val(qty);
                } else {
                    deliveredQty.prop('readonly', false);
                }
            });
        });

        $(document).on('input', '.qty', function() {
            let row = $(this).closest('tr');
            let type = $('#sale_type').val();
            let qty = $(this).val();

            if (type == 'RD') {
                row.find('.delivered_qty').val(qty);
            }
            updateRowAmount(row);
        });

        $(document).on('input', '.price', function() {
            let row = $(this).closest('tr');
            updateRowAmount(row);
        });

        function updateRowAmount(row) {
            let qty = parseFloat(row.find('.qty').val()) || 0;
            let price = parseFloat(row.find('.price').val()) || 0;
            row.find('.amount').val((qty * price).toFixed(2));
            updateTotal();
        }

        function updateTotal() {
            var total = 0;
            $(".amount").each(function() {
                var inputValue = $(this).val();
                total += parseFloat(inputValue) || 0;
            });

            $("#totalAmount").html(total.toFixed(2));

            var vat = parseFloat($("#vat").val()) || 0;
            var vat_amount = total * (vat / 100);
            $("#vat_amount").val(vat_amount.toFixed(2));
            $("#total_bill").val((total + vat_amount).toFixed(2));
        }

        $(document).on('click', '.remove_row', function() {
            if ($('#products_table tbody tr').length > 1) {
                $(this).closest('tr').remove();
                updateTotal();
            }
        });

        function deleteRow(id) {
            existingProducts = $.grep(existingProducts, function(value) {
                return value !== id;
            });
            $('#row_' + id).remove();
            updateTotal();
        }


        function calculatePayment() {
            var total = 0;
            $("input[id^='paymnet_amount_']").each(function() {
                var inputId = $(this).attr('id');
                var inputValue = $(this).val();
                total += parseFloat(inputValue);
            });

            $("#totalPayment").html(total.toFixed(2));
        }
    </script>
@endsection
