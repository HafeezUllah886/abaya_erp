@extends('layout.app')
@section('content')
    <div class="row">
        <!-- Default Datatable start -->
        <div class="col-12">
            <div class="card ">
                <div class="card-header d-flex justify-content-between">

                    <h5>{{ $type }}</h5>

                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#new">Add
                        {{ $type }}</button>
                </div>
                <div class="card-body p-0">
                    <div class="app-datatable-default overflow-auto app-scroll">
                        <table class="display app-data-table default-data-table" id="defaultDatatable">
                            <thead>
                                <tr>
                                    <th width="10px">#</th>
                                    <th class="text-start">Item</th>
                                    @if ($type == 'Ready Made')
                                        <th>SKU</th>
                                    @else
                                        <th>Unit</th>
                                    @endif
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Stock Value</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $key => $item)
                                    <tr>
                                        <td class="text-dark">{{ $key + 1 }}</td>
                                        <td class="text-start">{{ $item->name }}</td>
                                        @if ($type == 'Ready Made')
                                            <td>{{ $item->sku }}</td>
                                        @else
                                            <td>{{ $item->unit }}</td>
                                        @endif
                                        <td>{{ number_format($item->price, 2) }}</td>
                                        <td>{{ number_format(getStock($item->id, $type), 2) }}</td>
                                        <td>{{ number_format(itemStockValue($item->id, $type), 2) }}</td>
                                        <td>
                                            @if ($item->is_active == 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#edit{{ $item->id }}">Edit</button>
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#statement{{ $item->id }}">View Details</button>
                                        </td>
                                        <div id="edit{{ $item->id }}" class="modal fade" tabindex="-1"
                                            aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Edit {{ $type }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"> </button>
                                                    </div>
                                                    <form action="{{ route('products.update', $item->id) }}"
                                                        method="post">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" value="{{ $type }}">
                                                        <div class="modal-body">

                                                            <div class="form-group mt-2">
                                                                <label for="name">Name</label>
                                                                <input type="text" name="name" required
                                                                    value="{{ $item->name }}" id="name"
                                                                    class="form-control">
                                                            </div>

                                                            @if ($type == 'Ready Made')
                                                                <div class="form-group mt-2">
                                                                    <label for="sku">SKU</label>
                                                                    <input type="text" name="sku" required
                                                                        value="{{ $item->sku }}" id="sku"
                                                                        class="form-control">
                                                                </div>
                                                            @else
                                                                <div class="form-group mt-2">
                                                                    <label for="unit">Unit</label>
                                                                    <select name="unit" id="unit"
                                                                        class="form-control">
                                                                        <option value="Meter"
                                                                            {{ $item->unit == 'Meter' ? 'selected' : '' }}>
                                                                            Meter</option>
                                                                        <option value="Yard"
                                                                            {{ $item->unit == 'Yard' ? 'selected' : '' }}>
                                                                            Yard</option>
                                                                        <option value="Pcs"
                                                                            {{ $item->unit == 'Pcs' ? 'selected' : '' }}>
                                                                            Pcs</option>
                                                                        <option value="Rolls"
                                                                            {{ $item->unit == 'Rolls' ? 'selected' : '' }}>
                                                                            Rolls</option>
                                                                        <option value="Ltr"
                                                                            {{ $item->unit == 'Ltr' ? 'selected' : '' }}>
                                                                            Ltr</option>
                                                                        <option value="Nos"
                                                                            {{ $item->unit == 'Nos' ? 'selected' : '' }}>
                                                                            Nos</option>
                                                                    </select>
                                                                </div>
                                                            @endif

                                                            <div class="form-group mt-2">
                                                                <label for="price">Price</label>
                                                                <input type="number" step="any" name="price" required
                                                                    value="{{ $item->price }}" id="price"
                                                                    class="form-control">
                                                            </div>


                                                            <div class="form-group mt-2">
                                                                <label for="status">Status</label>
                                                                <select name="is_active" id="status"
                                                                    class="form-control">
                                                                    <option value="1"
                                                                        {{ $item->is_active == '1' ? 'selected' : '' }}>
                                                                        Active</option>
                                                                    <option value="0"
                                                                        {{ $item->is_active == '0' ? 'selected' : '' }}>
                                                                        Inactive</option>
                                                                </select>
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Update</button>
                                                        </div>
                                                    </form>
                                                </div><!-- /.modal-content -->
                                            </div><!-- /.modal-dialog -->
                                        </div><!-- /.modal -->

                                        <div id="statement{{ $item->id }}" class="modal fade" tabindex="-1"
                                            aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">View Stock Details
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"> </button>
                                                    </div>
                                                    <form action="{{ route('stock.show', $item->id) }}" method="get">
                                                        @csrf
                                                        <input type="hidden" name="id"
                                                            value="{{ $item->id }}">
                                                        <input type="hidden" name="type"
                                                            value="{{ $type }}">
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group mt-2">
                                                                        <label for="from_date">From Date</label>
                                                                        <input type="date" name="from" required
                                                                            value="{{ firstDayOfMonth() }}"
                                                                            id="from" class="form-control">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group mt-2">
                                                                        <label for="to_date">To Date</label>
                                                                        <input type="date" name="to" required
                                                                            value="{{ date('Y-m-d') }}" id="to"
                                                                            class="form-control">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">View</button>
                                                        </div>
                                                    </form>
                                                </div><!-- /.modal-content -->
                                            </div><!-- /.modal-dialog -->
                                        </div><!-- /.modal -->

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Default Datatable end -->



    </div>
    <!-- Default Modals -->

    <div id="new" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create New {{ $type }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('products.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div class="modal-body">

                        <div class="form-group mt-2">
                            <label for="name">Name</label>
                            <input type="text" name="name" required id="name" class="form-control">
                        </div>

                        @if ($type == 'Ready Made')
                            <div class="form-group mt-2">
                                <label for="sku">SKU</label>
                                <input type="text" name="sku" required id="sku" class="form-control">
                            </div>
                        @else
                            <div class="form-group mt-2">
                                <label for="unit">Unit</label>
                                <select name="unit" id="unit" class="form-control">
                                    <option value="Meter">Meter</option>
                                    <option value="Yard">Yard</option>
                                    <option value="Pcs">Pcs</option>
                                    <option value="Rolls">Rolls</option>
                                    <option value="Ltr">Ltr</option>
                                    <option value="Nos">Nos</option>
                                </select>
                            </div>
                        @endif

                        <div class="form-group mt-2">
                            <label for="price">Price</label>
                            <input type="number" step="any" name="price" required value="" min="0"
                                id="price" class="form-control">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@section('page-css')
    <!-- data table css -->
    <link href="{{ asset('assets/vendor/datatable/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('page-js')
    <!-- data table js -->
    <script src="{{ asset('assets/vendor/datatable/jquery-3.5.1.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/datatable2/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/datatable2/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/datatable2/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/datatable2/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/datatable2/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/datatable2/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/js/data_table.js') }}"></script>
@endsection
