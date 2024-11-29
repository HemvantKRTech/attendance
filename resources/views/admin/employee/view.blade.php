@extends('admin.layouts.master')
@push('links')

@endpush




@section('main')



        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{Str::title(str_replace('-', ' ', request()->segment(2)))}}</h4>
                    @can('add_client')
                    <div class="page-title-right">
                        <a href="{{ route('admin.'.request()->segment(2).'.create') }}"  class="btn-sm btn btn-primary btn-label rounded-pill">
                            <i class="bx bx-plus label-icon align-middle rounded-pill fs-16 me-2"></i>
                            Add {{Str::title(str_replace('-', ' ', request()->segment(2)))}}
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
                            <!-- Employee Information -->
                            <tr>
                                <th width="15%">Avatar</th>
                                <td colspan="3">
                                    <img class="avatar-img img-fluid d-block avatar-xl img-thumbnail rounded" src="{{ asset($employee->avatar) }}" alt="Employee Avatar">
                                </td>
                            </tr>
                        
                            <tr>
                                <th>Name</th>
                                <td>{{ $employee->name }}</td>
                                <th>Email</th>
                                <td>{{ $employee->email }}</td>
                            </tr>
                        
                            <tr>
                                <th>Mobile No.</th>
                                <td>{{ $employee->mobile }}</td>
                                <th>Gender</th>
                                <td>{{ $employee->gender }}</td>
                            </tr>
                        
                            <tr>
                                <th>Date of Birth</th>
                                <td>{{ $employee->date_of_birth }}</td>
                                <th>Company</th>
                                <td>{{ $employee->company->name }}</td>
                            </tr>
                        
                            <tr>
                                <th>Employee Code</th>
                                <td>{{ $employeeDetails->employee_code }}</td> <!-- New Field -->
                                <th>Department</th>
                                <td>{{ $department->name }}</td> <!-- New Field -->
                            </tr>
                        
                            <tr>
                                <th>Designation</th>
                                <td>{{ $employeeDetails->designation }}</td> <!-- New Field -->
                                <th>Father/Husband Name</th>
                                <td>{{ $employeeDetails->fathername }}</td>
                            </tr>
                        
                            <tr>
                                <th>Aadhar No.</th>
                                <td>{{ $employeeDetails->aadhar_no }}</td>
                                <th>Bank Account No.</th>
                                <td>{{ $employeeDetails->ac_no }}</td>
                            </tr>
                        
                            <tr>
                                <th>Bank Name</th>
                                <td>{{ $employeeDetails->bank_name }}</td>
                                <th>IFSC Code</th>
                                <td>{{ $employeeDetails->ifs_code }}</td>
                            </tr>
                        
                            <tr>
                                <th>ESIC No.</th>
                                <td>{{ $employeeDetails->esic_no }}</td>
                                <th>EPF No.</th>
                                <td>{{ $employeeDetails->epf_no }}</td>
                            </tr>
                        
                            <tr>
                                <th>Date of Joining</th>
                                <td>{{ $employee->date_of_birth }}</td>
                                <th>Date of Relieving</th>
                                <td>{{ $employeeDetails->date_of_relieving ? $employeeDetails->date_of_relieving : 'N/A' }}</td>
                            </tr>
                        
                            <tr>
                                <th>Location</th>
                                <td>{{ $employeeDetails->location }}</td>
                                <th>Nationality</th>
                                <td>{{ $employeeDetails->nationality }}</td>
                            </tr>
                        </table>
                        
                        
                        
                    </div>
                </div>
            </div><!--end col-->
        </div><!--end row-->



@endsection


@push('scripts')


@endpush