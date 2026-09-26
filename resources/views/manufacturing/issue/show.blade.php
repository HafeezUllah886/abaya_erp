@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>View Issue Voucher #{{ $voucher->id }}</h5>
                    <a href="{{ route('issue_vouchers.index') }}" class="btn btn-secondary">Back</a>
                </div>
                <div class="card-body">
                    <p><strong>Tailor:</strong> {{ $voucher->tailor->title ?? 'N/A' }}</p>
                    <p><strong>Date:</strong> {{ date('d-m-Y', strtotime($voucher->date)) }}</p>
                    <p><strong>Status:</strong> {{ $voucher->status }}</p>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Cost at issue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($voucher->details as $detail)
                                <tr>
                                    <td>{{ $detail->product->name ?? 'N/A' }}</td>
                                    <td>{{ $detail->qty }}</td>
                                    <td>{{ $detail->cost_at_issue }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total</td>
                                <td>{{ $voucher->details->sum('qty') }}</td>
                                <td>{{ $voucher->details->sum('cost_at_issue') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
