@extends('admin.layouts.master')
@push('links')
<link rel="stylesheet" href="{{ asset('admin-assets/libs/dropify/css/dropify.min.css') }}">
@endpush

@section('main')

<style>
    .modal {
        --vz-modal-width: 800px !important;
    }
    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875rem;
        display: block;
        margin-top: 0.25rem;
    }
    .is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + .75rem);
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                {{-- @can('add_salary')
                <div class="page-title-right">
                    <a href="{{ route('admin.'.request()->segment(2).'.allsalary') }}" class="btn-sm btn btn-primary btn-label rounded-pill">
                        <i class="bx bx-plus label-icon align-middle rounded-pill fs-16 me-2"></i>
                        Export Company {{ Str::title(str_replace('-', ' ', request()->segment(2))) }} Sheet
                    </a>
                </div>
                @endcan --}}

                <div class="row my-1">
                    <div class="col-lg-12 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-header bg-transparent border-primary">
                                <h5 class="my-0 text-primary">Export salary sheet</h5>
                            </div>
                        </div>
                    </div>
                </div>

                {{ html()->form('POST', route('admin.' . request()->segment(2) . '.store'))
                    ->class('form-horizontal')
                    ->attribute('id', 'wageform')
                    ->attribute('enctype', 'multipart/form-data')
                    ->open() }}

                    <div class="row my-1">
                        <div class="col-lg-12 col-sm-12 col-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        @php
                                            $years = range(date('Y') - 5, date('Y'));
                                            $months = [
                                                'January' => 'January',
                                                'February' => 'February',
                                                'March' => 'March',
                                                'April' => 'April',
                                                'May' => 'May',
                                                'June' => 'June',
                                                'July' => 'July',
                                                'August' => 'August',
                                                'September' => 'September',
                                                'October' => 'October',
                                                'November' => 'November',
                                                'December' => 'December',
                                            ];
                                        @endphp

                                        <div class="row mb-3">
                                            <div class="col-md-6 form-group">
                                                {{ html()->label('Select Year')->for('year') }}
                                                {{ html()->select('year', array_combine($years, $years))
                                                    ->class('form-control')
                                                    ->required()
                                                    ->placeholder('Select Year')
                                                    ->value(old('year')) }}
                                                @error('year')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 form-group">
                                                {{ html()->label('Select Month')->for('month') }}
                                                {{ html()->select('month', $months)
                                                    ->class('form-control')
                                                    ->required()
                                                    ->placeholder('Select Month')
                                                    ->value(old('month')) }}
                                                @error('month')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6 form-group">
                                                {{ html()->label('Select Company')->for('company_id') }}
                                                {{ html()->select('company_id', $companies)
                                                    ->class('form-control')
                                                    ->required()
                                                    ->placeholder('Select Company')
                                                    ->value(old('company_id')) }}
                                                @error('company_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-3 form-group text-end">
                                                {{ html()->submit('Upload')->class('btn btn-primary') }}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

{{ html()->form()->close() }}
</div>
        </div>

{{-- @if ($tempsalary->isNotEmpty())
    <div class="row my-1">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="my-0 text-primary">Salary Data</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Working Days</th>
                                <th>Rate of pay</th>
                                <th>Total Deduction</th>
                                <th>Total Payable</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tempsalary as $salary)
                                <tr>
                                    <td>{{ $salary->employee->name }}</td>
                                    <td>{{ $salary->employee->email }}</td>
                                    <td>{{ $salary->working_days }}</td>
                                    <td>{{ $salary->rate_of_pay }}</td>
                                    <td>{{ $salary->total_deductions }}</td>
                                    <td>{{ $salary->net_payable }}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm view-details" 
                                                data-id="{{ $salary->admin_id }}">View Details</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="text-end">
                        <form method="POST" action="{{ route('admin.' . request()->segment(2) . '.verify') }}">
                            @csrf
                            <button type="submit" class="btn btn-success" id="verifyButton">Verify</button>
                        </form>
                        <form method="POST" action="{{ route('admin.' . request()->segment(2) . '.cancel') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger" id="cancelButton">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if(session('not_found_aadhars'))
    <div class="alert alert-warning">
        <strong>Not Found Aadhar Numbers:</strong>
        <ul>
            @foreach(session('not_found_aadhars') as $aadhar)
                <li>{{ $aadhar }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="modal fade" id="employeeDetailsModal" tabindex="-1" role="dialog" aria-labelledby="employeeDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeDetailsModalLabel">Employee Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="employee-details-content">
                </div>
            </div>
        </div>
    </div>
</div> --}}

@endsection

@push('scripts')
{{-- <script>
    document.addEventListener('DOMContentLoaded', () => {
        // For Bootstrap 5 modals, we use the following initialization
        const employeeDetailsModal = new bootstrap.Modal(document.getElementById('employeeDetailsModal'));

        // Handle click events on all 'view-details' buttons
        document.querySelectorAll('.view-details').forEach(button => {
            button.addEventListener('click', function() {
                const adminId = this.dataset.id;

                // Fetch employee details
                fetch(`/admin/salary/details/${adminId}`)
                    .then(response => response.json())
                    .then(data => {
                        // console.log(data);
                        if ( data.employee.length > 0) {
                            const employee = data.employee[0];
                            const details = `
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Employee Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4"><p><strong>Name:</strong> ${employee.employee.name}</p></div>
                                            <div class="col-md-4"><p><strong>Email:</strong> ${employee.employee.email}</p></div>
                                            <div class="col-md-4"><p><strong>Mobile:</strong> ${employee.employee.mobile}</p></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mt-3">
                                    <div class="card-header bg-info text-white">
                                        <h5>Salary Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Working Days</th>
                                                    <th>Rate of Pay</th>
                                                    <th>Total Deductions</th>
                                                    <th>Net Payable</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>${employee.working_days}</td>
                                                    <td>${employee.rate_of_pay}</td>
                                                    <td>${employee.total_deductions}</td>
                                                    <td>${employee.net_payable}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>`;

                            document.getElementById('employee-details-content').innerHTML = details;

                            // Show the modal using Bootstrap 5 method
                            employeeDetailsModal.show();
                        } else {
                            // Display an alert if no data is found
                            alert('Employee details not found.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while fetching employee details.');
                    });
            });
        });
    });
</script> --}}
@endpush

