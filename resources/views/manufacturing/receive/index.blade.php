@extends('layout.app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Receive Vouchers History</h5>
                <button aria-controls="canvasEnd" class="btn btn-primary" data-bs-target="#canvasEnd" data-bs-toggle="offcanvas" type="button">Filter</button>
            </div>
            <div class="card-body p-0">
                <div class="app-datatable-default overflow-auto app-scroll">
                    <table class="display app-data-table default-data-table" id="defaultDatatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Tailor</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vouchers as $key => $voucher)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ date('d-m-Y', strtotime($voucher->date)) }}</td>
                                <td>{{ $voucher->tailor->title ?? 'N/A' }}</td>
                                <td>{{ $voucher->status }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('receive_vouchers.show', $voucher->id) }}"><i class="ti ti-eye me-2 text-secondary"></i> View</a></li>
                                            <li><a class="dropdown-item text-danger" href="{{ route('receive_vouchers.delete', $voucher->id) }}"><i class="ti ti-trash me-2 text-danger"></i> Delete</a></li>
                                        </ul>
                                    </div>
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

@section('filter-content')
<div class="mb-3">
    <div class="input-group">
        <span class="input-group-text"><i class="ti ti-calendar"></i></span>
        <input type="date" class="form-control" name="from" value="{{ $from }}">
    </div>
</div>
<div class="mb-3">
    <div class="input-group">
        <span class="input-group-text"><i class="ti ti-calendar"></i></span>
        <input type="date" class="form-control" name="to" value="{{ $to }}">
    </div>
</div>
<div class="mb-3">
    <div class="input-group">
        <span class="input-group-text"><i class="ti ti-user"></i></span>
        <select class="form-control" name="tailor">
            <option value="all">All Tailors</option>
            @foreach ($tailors as $t)
                <option value="{{ $t->id }}" {{ $tailor == $t->id ? 'selected' : '' }}>{{ $t->title }}</option>
            @endforeach
        </select>
    </div>
</div>
@endsection
@include('layout.offcan')
@endsection

@section('page-css')
<link href="{{ asset('assets/vendor/datatable/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('page-js')
<script src="{{ asset('assets/vendor/datatable/jquery-3.5.1.js') }}"></script>
<script src="{{ asset('assets/vendor/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/data_table.js') }}"></script>
@endsection
