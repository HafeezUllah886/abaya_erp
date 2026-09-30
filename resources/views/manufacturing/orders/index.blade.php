@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Manufacturing Orders</h4>
                        <a href="{{ route('manufacturing_orders.create') }}" class="btn btn-primary">Create New Order</a>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('manufacturing_orders.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="date" name="from" class="form-control" value="{{ $from }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="to" class="form-control" value="{{ $to }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="tailor" class="form-control">
                                        <option value="all">All Tailors</option>
                                        @foreach ($tailors as $t)
                                            <option value="{{ $t->id }}" {{ $tailor == $t->id ? 'selected' : '' }}>
                                                {{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-info">Filter</button>
                                </div>
                            </div>
                        </form>

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Tailor</th>
                                    <th>Payment Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->date }}</td>
                                        <td>{{ $order->tailor->title ?? 'N/A' }}</td>
                                        <td>{{ $order->payment_type }}</td>
                                        <td>{{ $order->total_amount }}</td>
                                        <td>
                                            @php
                                                $total_ordered = $order->products->sum('order_qty');
                                                $total_received = $order->products->sum('received_qty');
                                                $is_completed = $total_ordered > 0 && $total_received >= $total_ordered;
                                            @endphp
                                            @if ($is_completed)
                                                <span class="badge bg-success">Completed</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->notes }}</td>
                                        <td>
                                            <a href="{{ route('manufacturing_orders.show', $order->id) }}"
                                                class="btn btn-sm btn-info">View</a>
                                            <a href="{{ route('manufacturing_orders.edit', $order->id) }}"
                                                class="btn btn-sm btn-primary">Edit</a>
                                            <a href="{{ route('manufacturing_orders.delete', $order->id) }}"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
                                        </td>
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
