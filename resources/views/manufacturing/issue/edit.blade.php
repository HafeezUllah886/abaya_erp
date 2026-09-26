@extends('layout.app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Edit Issue Voucher #{{ $voucher->id }}</h5>
                <a href="{{ route('issue_vouchers.index') }}" class="btn btn-secondary">Back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('issue_vouchers.update', $voucher->id) }}" method="post">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Tailor</label>
                            <select name="tailor_id" class="form-control" required>
                                @foreach ($tailors as $t)
                                    <option value="{{ $t->id }}" {{ $voucher->tailor_id == $t->id ? 'selected' : '' }}>{{ $t->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" value="{{ $voucher->date }}" required>
                        </div>
                    </div>
                    <div class="alert alert-warning">Note: Editing items directly is complex. Please delete and recreate the voucher to adjust items in this version.</div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('issue_vouchers.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
