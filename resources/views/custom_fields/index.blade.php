@extends('layouts.master')
@section('title')
    Custom Field
@endsection
@section('css')
    <link href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
@endsection
@section('content')
    <x-navbar />
    <div class="container my-5">
        <x-alert />
        <div class="row mb-4">
            <div class="col-md-6">
                <span class="h5" style="cursor: pointer;" onclick="history.back()"><i class="fa fa-arrow-left" aria-hidden="true"></i></span>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary add" data-bs-toggle="modal" data-bs-target="#add_custom_field">
                    Add Custom Field
                </button>
            </div>
        </div>
        <table class="table table-striped" data-url="{{ route('custom-field.index') }}" id="custom_field_table">
            <thead>
                <tr>
                    <th>
                        Field name
                    </th>
                    <th>
                        Field type
                    </th>
                    <th>
                        Is Required
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
    <div class="modal fade" id="add_custom_field">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Add Custom Field</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form id="custom_field_form" action="{{ route('custom-field.store') }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Field</label>
                                <select class="form-control form-control-sm" name="field_id">
                                    <option value="">Select Field</option>
                                    @foreach ($fields as $field)
                                        <option value="{{ $field->id }}">{{ $field->type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Field name</label>
                                <input type="text" name="label" class="form-control form-control-sm" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Is Required</label>
                                <select class="form-control form-control-sm" name="is_required">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="mb-3 text-end">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                <button class="btn btn-primary btn-sm submit_btn">Add Field</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <script src="{{ asset('assets/js/common.js') }}"></script>
    <script>
        let storeUrl = "{{ route('custom-field.store') }}";
    </script>
    <script src="{{ asset('assets/js/custom_field.js') }}"></script>
@endsection
