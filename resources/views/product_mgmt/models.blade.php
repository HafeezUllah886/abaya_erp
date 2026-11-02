@extends('layout.app')
@section('content')
    <div class="row">
        <!-- Default Datatable start -->
        <div class="col-12">
            <div class="card ">
                <div class="card-header d-flex justify-content-between">

                    <h5>Abaya Models</h5>

                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#new">Add
                        Model</button>
                </div>
                <div class="card-body p-0">
                    <div class="app-datatable-default overflow-auto app-scroll">
                        <table class="display app-data-table default-data-table" id="defaultDatatable">
                            <thead>
                                <tr>
                                    <th width="10px">#</th>
                                    <th class="text-start">Abaya</th>
                                    <th>Model</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($models as $key => $model)
                                    <tr>
                                        <td class="text-dark">{{ $key + 1 }}</td>
                                        <td class="text-start">{{ $model->product->name ?? 'N/A' }}</td>
                                        <td>{{ $model->model_number }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#edit{{ $model->id }}">Edit</button>

                                        </td>
                                        <div id="edit{{ $model->id }}" class="modal fade" tabindex="-1"
                                            aria-labelledby="editModalLabel{{ $model->id }}" aria-hidden="true" style="display: none;">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editModalLabel{{ $model->id }}">Edit Model</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"> </button>
                                                    </div>
                                                    <form action="{{ route('models.update', $model->id) }}" method="post" class="prevent-double-submit">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">

                                                            <div class="form-group mt-2">
                                                                <label for="product_{{ $model->id }}">Abaya</label>
                                                                <select name="product_id" id="product_{{ $model->id }}" class="form-control" required>
                                                                    @foreach ($products as $product)
                                                                        <option value="{{ $product->id }}"
                                                                            {{ $model->product_id == $product->id ? 'selected' : '' }}>
                                                                            {{ $product->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group mt-2">
                                                                <label for="model_number_{{ $model->id }}">Model Number</label>
                                                                <input type="text" name="model_number" required
                                                                    value="{{ $model->model_number }}" id="model_number_{{ $model->id }}"
                                                                    class="form-control">
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

    <div id="new" class="modal fade" tabindex="-1" aria-labelledby="newModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newModalLabel">Create New Model</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('models.store') }}" method="post" class="prevent-double-submit">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mt-2">
                            <label for="new_product_id">Abaya</label>
                            <select name="product_id" id="new_product_id" class="form-control" required>
                                <option value="">Select Abaya</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="new_model_number">Model Number</label>
                            <input type="text" name="model_number" required id="new_model_number" class="form-control" placeholder="Enter Model Number">
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
