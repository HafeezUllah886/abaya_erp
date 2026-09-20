@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card ">
                <div class="card-header d-flex justify-content-between">
                    <h5>Create Order Fulfillment</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('fulfillments.store') }}" method="post">
                        @csrf
                        <div class="row g-1">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="order_id">Select Order</label>
                                    <select name="order_id" class="w-100" id="order_id" required>
                                        <option value=""></option>
                                        @foreach ($orders as $order)
                                            <option value="{{ $order->id }}">Order #{{ $order->id }} - {{ $order->customer->title }} ({{ $order->date }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <th width="40%">Product</th>
                                        <th class="text-center">Ordered Qty</th>
                                        <th class="text-center">Already Fulfilled</th>
                                        <th class="text-center">Fulfill Now</th>
                                    </thead>
                                    <tbody id="order_details_list">
                                        <!-- Loaded via ajax -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="date">Fulfillment Date</label>
                                    <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}"
                                        class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary w-100">Fulfill Order</button>
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
        $('#order_id').select2({
            placeholder: "Select an Order",
            allowClear: true,
            width: '100%' 
        });
    });

     $("#order_id").on('select2:select', function(e) {
            var orderId = e.params.data.id;
            if (orderId) {
                getOrderDetails(orderId);
            }
        });

        function getOrderDetails(id) {
            $.ajax({
                url: "{{ url('fulfillments/getorder/') }}/" + id,
                method: "GET",
                success: function(response) {
                    var html = '';
                    response.details.forEach(function(detail) {
                        var remaining = detail.qty - detail.fulfilled_qty;
                        html += '<tr>';
                        html += '<td>' + detail.product_name + '</td>';
                        html += '<td class="text-center">' + detail.qty + '</td>';
                        html += '<td class="text-center">' + detail.fulfilled_qty + '</td>';
                        html += '<td class="p-0"><input type="number" name="fulfill_qty['+detail.product_id+']" min="0" max="'+remaining+'" step="any" value="'+remaining+'" class="form-control form-control-sm text-center p-1"></td>';
                        html += '</tr>';
                    });
                    $("#order_details_list").html(html);
                }
            });
        }
</script>
@endsection
