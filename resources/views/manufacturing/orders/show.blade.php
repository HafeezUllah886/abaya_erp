@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Manufacturing Order #{{ $order->id }}</h4>
                        <a href="{{ route('manufacturing_orders.index') }}" class="btn btn-secondary">Back to Orders</a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3"><strong>Date:</strong> <br> {{ $order->date }}</div>
                            <div class="col-md-3"><strong>Tailor:</strong> <br> {{ $order->tailor->title ?? 'N/A' }}</div>
                            <div class="col-md-3"><strong>Payment Type:</strong> <br> {{ $order->payment_type }}</div>
                            <div class="col-md-3"><strong>Amount:</strong> <br> {{ $order->total_amount }}</div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-12"><strong>Notes:</strong> <br> {{ $order->notes }}</div>
                        </div>

                        <h5>Finished Products (Abayas)</h5>
                        <table class="table table-bordered mb-4">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Order Qty</th>
                                    <th>Received Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->products as $p)
                                    <tr>
                                        <td>{{ $p->product_id }}</td>
                                        <td>{{ $p->order_qty }}</td>
                                        <td>{{ $p->received_qty }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <h5>Raw Materials</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Own Qty</th>
                                    <th>Customer Qty</th>
                                    <th>Cost Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->materials as $m)
                                    <tr>
                                        <td>{{ $m->product_id }}</td>
                                        <td>{{ $m->own_qty }}</td>
                                        <td>{{ $m->customer_qty }}</td>
                                        <td>{{ $m->cost_price }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
