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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($voucher->details as $detail)
                        <tr>
                            <td>{{ $detail->product->name ?? 'N/A' }}</td>
                            <td>{{ $detail->qty }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
