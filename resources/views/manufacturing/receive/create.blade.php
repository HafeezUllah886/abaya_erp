@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card ">
                <div class="card-header d-flex justify-content-between">
                    <h5>Receive Finished Products from Tailor</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('receive_vouchers.store') }}" method="post">
                        @csrf
                        <div class="row g-1">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="product">Finished Product</label>
                                    <select name="product" class="w-100" id="product">
                                        <option value=""></option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <th width="50%">Product</th>
                                        <th class="text-center">Qty Received</th>
                                        <th></th>
                                    </thead>
                                    <tbody id="products_list"></tbody>
                                </table>
                            </div>
                            
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="tailor_id">Tailor (Account)</label>
                                    <select name="tailor_id" id="tailor_id" class="select2 w-100" required>
                                        <option value=""></option>
                                        @foreach ($tailors as $tailor)
                                            <option value="{{ $tailor->id }}">{{ $tailor->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="amount">Production Amount</label>
                                    <input type="number" name="amount" id="amount" class="form-control" min="0" step="any" required>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="payment_status">Payment Status</label>
                                    <select name="payment_status" id="payment_status" class="form-control" required>
                                        <option value="Pending">Pending</option>
                                        <option value="Paid">Paid</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-4" id="account_div" style="display: none;">
                                <div class="form-group">
                                    <label for="account_id">Paid From (Business Account)</label>
                                    <select name="account_id" id="account_id" class="select2 w-100">
                                        <option value=""></option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary w-100">Create Receive Voucher</button>
                            </div>
                </div>
            </form>
                </div>
            </div>
        </div>
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

        $('#payment_status').on('change', function() {
            if($(this).val() == 'Paid') {
                $('#account_div').show();
                $('#account_id').attr('required', true);
            } else {
                $('#account_div').hide();
                $('#account_id').attr('required', false);
            }
        });
    });

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
                url: "{{ url('receive_vouchers/getproduct/') }}/" + id,
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
                        html += '<td class="p-0"><input type="number" name="qty[]" min="0.1" step="any" value="1" class="form-control form-control-sm text-center p-1" id="qty_' + id + '"></td>';
                        html += '<td class="p-0"> <span class="btn btn-sm btn-danger" onclick="deleteRow('+id+')">X</span> </td>';
                        html += '<input type="hidden" name="id[]" value="' + id + '">';
                        html += '</tr>';
                        $("#products_list").prepend(html);
                        existingProducts.push(id);
                    }
                }
            });
        }

        function deleteRow(id) {
            existingProducts = $.grep(existingProducts, function(value) {
                return value !== id;
            });
            $('#row_'+id).remove();
        }
</script>
@endsection
