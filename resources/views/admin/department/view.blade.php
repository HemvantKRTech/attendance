@extends('admin.layouts.master')

@push('links')
<!-- Add any additional CSS or links here if needed -->
@endpush

@section('main')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $department->name }} Details</h4>
            @can('add_department')
            <div class="page-title-right">
                <a href="{{ route('admin.department.create') }}" class="btn-sm btn btn-primary btn-label rounded-pill">
                    <i class="bx bx-plus label-icon align-middle rounded-pill fs-16 me-2"></i>
                    Add Department
                </a>
            </div>
            @endcan
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <table class="table align-middle" style="width:100%">
                    <!-- Department Information -->
                    <tr>
                        <th width="25%">Department Name</th>
                        <td>{{ $department->name }}</td>
                        <th width="25%">Company</th>
                        <td>{{ $department->company->name ?? 'N/A' }}</td>
                    </tr>

                    {{-- <tr>
                        <th>Department ID</th>
                        <td>{{ $department->id }}</td>
                        <th>Company Email</th>
                        <td>{{ $department->company->email ?? 'N/A' }}</td>
                    </tr> --}}

                   

                    <!-- Add more fields as needed -->
                </table>
            </div>
        </div>
    </div><!--end col-->
</div><!--end row-->

@endsection

@push('scripts')
<!-- Add any additional JS here if needed -->
@endpush
