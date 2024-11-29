@extends('admin.layouts.master')

@push('links')
<link rel="stylesheet" href="{{ asset('admin-assets/libs/dropify/css/dropify.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('main')

<!-- Start Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ Str::title(str_replace('-', ' ', request()->segment(2))) }} Details</h4>
            <div class="page-title-right">
                <a href="{{ route('admin.' . request()->segment(2) . '.index') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-arrow-back"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>
<!-- End Page Title -->

<!-- Start User Details Table -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Admin Details</h5>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 30%;">Name</th>
                            <td>{{ $admin->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $admin->email }}</td>
                        </tr>
                        <tr>
                            <th>Contact Number</th>
                            <td>{{ $admin->mobile }}</td>
                        </tr>
                        <tr>
                            <th>Gender</th>
                            <td>{{ ucfirst($admin->gender) }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($admin->status == 1)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Date of Birth</th>
                            <td>{{ \Carbon\Carbon::parse($admin->date_of_birth)->format('d M, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>{{ $admin->role->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Avatar</th>
                            <td>
                                <img src="{{ asset($admin->avatar) }}" alt="Avatar" class="img-thumbnail" style="max-width: 150px;">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- End User Details Table -->

@endsection

@push('scripts')
<script src="{{ asset('admin-assets/libs/dropify/js/dropify.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endpush
