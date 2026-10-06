@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>Bookmarks Settings</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('bookmarks.update') }}" method="post">
                        @csrf
                        <div class="form-group mt-2">
                            <label class="mb-3 d-block f-w-600">Select Bookmarked Menu Links</label>
                            
                            <div class="row">
                                @foreach($availableLinks as $route => $label)
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="bookmarks[]" value="{{ $route }}" id="bookmark_{{ $route }}" {{ is_array(auth()->user()->bookmarks) && in_array($route, auth()->user()->bookmarks) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="bookmark_{{ $route }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100">Save Bookmarks</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
