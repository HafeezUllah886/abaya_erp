@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>Activity Report</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('reportActivityData') }}" method="get">
                        @csrf
                        <div class="row">
                            <div class="form-group mt-2">
                                <label for="from">From Date</label>
                                <input type="date" name="from" required value="{{ firstDayOfMonth() }}" id="from" class="form-control">
                            </div>

                            <div class="form-group mt-2">
                                <label for="to">To Date</label>
                                <input type="date" name="to" required value="{{ date('Y-m-d') }}" id="to" class="form-control">
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary w-100">View Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
