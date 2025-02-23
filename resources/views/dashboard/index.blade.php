@extends('layouts.master')
@section('title')
    Dashboard
@endsection
@section('css')
    <link href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
@endsection
@section('content')
    <x-navbar />
    <div class="container my-5">
        <x-alert />
        <div class="row mb-4">
            <div class="col-md-12 text-end">
                <button class="btn btn-primary add" data-bs-toggle="modal" data-bs-target="#add_new_contact">Add New
                    Contact</button>
                <a href="{{ route('custom-field.index') }}" class="btn btn-success">Add Custom Field</a>
            </div>
        </div>
        <table class="table table-striped" id="contact_table" data-url="{{ route('dashboard.index') }}">
            <thead>
                <tr>
                    <th>
                        Name
                    </th>
                    <th>
                        Email
                    </th>
                    <th>
                        Phone
                    </th>
                    <th>
                        Gender
                    </th>
                    <th>
                        Profile Image
                    </th>
                    <th>
                        Status
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

    <div class="modal fade" id="add_new_contact">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Add Contact</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form method="post" action="{{ route('dashboard.store') }}" enctype="multipart/form-data"
                            id="add_contact_form">
                            @csrf
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control form-control-sm" name="name" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control form-control-sm" name="email" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="" class="form-control form-control-sm" name="phone" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gender</label>
                                    <div>
                                        @foreach ($genders as $gender)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="gender_id"
                                                    id="{{ $gender->slug }}" value="{{ $gender->id }}">
                                                <label class="form-check-label" for="{{ $gender->slug }}">
                                                    {{ $gender->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Profile Image</label>
                                    <input type="file" class="form-control form-control-sm" name="profile_image" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Additional File</label>
                                    <input type="file" class="form-control form-control-sm" name="additional_document" />
                                </div>
                            </div>
                            @if ($custom_fields->count() > 0)
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Additional Information</h5>
                                    </div>
                                    @foreach ($custom_fields as $field)
                                        <div class="col-md-6">
                                            <label class="form-label">{{ $field->label }}</label>
                                            @if ($field->field->type == 'textarea')
                                                <textarea class="form-control form-control-sm" name="additional_information_{{ $field->slug }}"></textarea>
                                            @else
                                                <input type="{{ $field->field->type }}" class="form-control form-control-sm"
                                                    name="additional_information_{{ $field->slug }}" />
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-md-12 text-end">
                                    <button type="button" class="btn btn-secondary me-2"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary submit_btn">Add Contact</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="contact_merged_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Merge Contact</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form method="post" action="{{ route('mergeContact') }}" id="contact_merge_form">
                            @csrf
                            <input type="hidden" class="contact_id" name="contact_id" />
                            <div class="mb-3">
                                <label class="form-label">Select Contact</label>
                                <select class="form-control form-control-sm contact_list"
                                    name="parent_contact_id"></select>
                            </div>
                            <div class="mb-3 text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Merge</button>
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
    <script>
        let storeUrl = "{{ route('dashboard.store') }}";
    </script>
    <script src="{{ asset('assets/js/common.js') }}"></script>
    <script src="{{ asset('assets/js/contacts.js') }}"></script>
@endsection
