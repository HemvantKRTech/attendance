@extends('admin.layouts.master')
@push('links')
<link rel="stylesheet" href="{{asset('admin-assets/libs/dropify/css/dropify.min.css')}}"> 
@endpush

@section('main')
<style>
    /* Style for invalid feedback messages */
    .invalid-feedback {
        color: #dc3545; /* Bootstrap's red color for error messages */
        font-size: 0.875rem; /* Slightly smaller font size */
        display: block; /* Ensure the message is displayed as a block element */
        margin-top: 0.25rem; /* Space above the message */
    }

    /* Optional: Style for form control elements with errors */
    .is-invalid {
        border-color: #dc3545; /* Red border for invalid fields */
        padding-right: calc(1.5em + .75rem); /* Space for the error icon if needed */
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <!-- Title Outside the Form -->
                <div class="row my-1">
                    <div class="col-lg-12 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-header bg-transparent border-primary">
                                <h5 class="my-0 text-primary">Upload Employee Sheet</h5>
                            </div>
                        </div>
                    </div>
                </div>
                @if(session('warning'))
                <div class="alert alert-warning">
                    <strong>{{ session('warning') }}</strong>
                    <ul>
                        @foreach(session('existing_aadhars') as $aadhar)
                            <li>{{ $aadhar }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
                <!-- Form Starts Here -->
                <div style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Father/Husband Name</th>
                                <th>Gender</th>
                                <th>Aadhar No</th>
                                <th>Mobile</th>
                                <th>Bank Account No</th>
                                <th>Bank Name</th>
                                <th>IFSC Code</th>
                                <th>ESIC No</th>
                                <th>PF No</th>
                                <th>Date of Birth</th>
                                <th>Date of Joining</th>
                                <th>Date of Relieving</th>
                                <th>Location</th>
                                <th>Nationality</th>
                                <th>Designation</th>
                                <th>Basic</th>
                                <th>PF Basic</th>
                                <th>HRA</th>
                                <th>Allowance</th>
                                <th>LWF</th>
                                <th>Deduction</th>
                                <th>Conveyance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                <tr class="{{ isset($repeatedAadhars[$employee->aadhar_no]) ? 'table-warning' : (in_array($employee->aadhar_no, $existingAadhars) ? 'table-danger' : '') }}">
                                    <td>{{ $employee->employee_name }}</td>
                                    <td>{{ $employee->father_or_husband_name }}</td>
                                    <td>{{ $employee->gender }}</td>
                                    <td>{{ $employee->aadhar_no }}</td>
                                    <td>{{ $employee->mobile }}</td>
                                    <td>{{ $employee->bank_account_no }}</td>
                                    <td>{{ $employee->bank_name }}</td>
                                    <td>{{ $employee->ifsc_code }}</td>
                                    <td>{{ $employee->esic_no }}</td>
                                    <td>{{ $employee->pf_no }}</td>
                                    <td>{{ $employee->date_of_birth }}</td>
                                    <td>{{ $employee->date_of_joining }}</td>
                                    <td>{{ $employee->date_of_relieving }}</td>
                                    <td>{{ $employee->location }}</td>
                                    <td>{{ $employee->nationality }}</td>
                                    <td>{{ $employee->designation }}</td>
                                    <td>{{ $employee->basic }}</td>
                                    <td>{{ $employee->pf_basic }}</td>
                                    <td>{{ $employee->hra }}</td>
                                    <td>{{ $employee->allowance }}</td>
                                    <td>{{ $employee->lwf }}</td>
                                    <td>{{ $employee->deduction }}</td>
                                    <td>{{ $employee->conveyance }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <form action="{{route('admin.employee.verify')}}" method="POST">
                        @csrf
                        <input type="hidden" name="company_id" value="{{ $employee->company_id }}">
                        <input type="hidden" name="employee_data" value="{{ json_encode($employees) }}">
                        <button type="submit" class="btn btn-primary">Verify</button>
                        <a href="" class="btn btn-secondary">Cancel/Review Again</a>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@endpush
