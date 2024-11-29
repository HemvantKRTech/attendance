@extends('admin.layouts.master')
@push('links')
<link rel="stylesheet" href="{{asset('admin-assets/libs/dropify/css/dropify.min.css')}}"> 
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

                {{ html()->form('PUT', route('admin.' . request()->segment(2) . '.update', $employee->id))
                ->class('form-horizontal')
                ->attribute('id', 'employeeForm')
                ->attribute('files', true)
                ->open() }}
            
                
<div class="row my-1">
    <div class="col-lg-12 col-sm-12 col-12">
        <div class="card">
            <div class="card-content">
                
                <div class="card-body">
                    <div class="card-header bg-transparent border-primary">
                        <h5 class="my-0 text-primary">General Information</h5>
                    </div>
                    <!-- Role Selection -->
                    <div class="row">
                            <div class="col-md-4 my-3 form-group">
                                {{ html()->label('Employee Name <span class="text-danger">*</span>')->for('name')->class('required-label') }}
                                {{ html()->text('name')->class('form-control')->required()->placeholder('Employee Name') ->value(old('name', $employee->name)) }}
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 my-3 form-group">
                                {{ html()->label('Gender <span class="text-danger">*</span>')->for('gender')->class('required-label') }}
                                {{ html()->select('gender', ['Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other'])->class('form-control')->value(old('gender', $employee->gender)) ->required()->placeholder('Select Gender') }}
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 my-3 form-group">
                                {{ html()->label('Father or Husband Name <span class="text-danger">*</span>')->for('fathername')->class('required-label') }}
                                {{ html()->text('fathername')->class('form-control')->required()->placeholder('Father or Husband Name') ->value(old('fathername', $employee->employeedetail->fathername)) }}
                                @error('fathername')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3 form-group">
                            {{ html()->label('Mobile No <span class="text-danger">*</span>')->for('mobile')->class('required-label') }}
                            {{ html()->text('mobile')->class('form-control')->required()->placeholder('Mobile No')->value(old('mobile', $employee->mobile)) }}
                            @error('mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            {{ html()->label('Email <span class="text-danger">*</span>')->for('email')->class('required-label') }}
                            {{ html()->email('email')->class('form-control')->required()->placeholder('Email')->value(old('email', $employee->email)) }}
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            {{ html()->label('Aadhar No <span class="text-danger">*</span>')->for('aadhar_no')->class('required-label') }}
                            {{ html()->text('aadhar_no')->class('form-control')->required()->placeholder('Aadhar No')->value(old('aadhar_no', $employee->employeedetail->aadhar_no)) }}
                            @error('aadhar_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-header bg-transparent border-primary">
                        <h5 class="my-0 text-primary">Bank Details</h5>
                    </div>
                    <div class="row">
                        <div class="col-md-4 my-3 form-group">
                            {{ html()->label('Bank Name <span class="text-danger">*</span>')->for('bank_name')->class('required-label') }}
                            {{ html()->text('bank_name')->class('form-control')->required()->placeholder('Bank Name')->value(old('bank_name', $employee->employeedetail->bank_name)) }}
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 my-3 form-group">
                            {{ html()->label('Bank Account No <span class="text-danger">*</span>')->for('ac_no')->class('required-label') }}
                            {{ html()->text('ac_no')->class('form-control')->required()->placeholder('Bank Account No') ->value(old('ac_no', $employee->employeedetail->ac_no)) }}
                            @error('ac_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 my-3 form-group">
                            {{ html()->label('IFSC Code <span class="text-danger">*</span>')->for('ifsc_code')->class('required-label') }}
                            {{ html()->text('ifsc_code')->class('form-control')->required()->placeholder('IFSC Code')->value(old('ifsc_code', $employee->employeedetail->ifs_code)) }}
                            @error('ifsc_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="card-header bg-transparent border-primary">
                        <h5 class="my-0 text-primary">Company Details</h5>
                    </div>
                    <div class="row">
                        <div class="col-md-4 my-3 form-group">
                            {{ html()->label('Company <span class="text-danger">*</span>')->for('company_id')->class('required-label') }}
                            {{ html()->select('company_id', $companies->pluck('name', 'id'))->class('form-control')->required()->placeholder('Select Company')->value(old('company_id', $employee->company_id)) }}
                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 my-3 form-group">
                            {{ html()->label('Skill <span class="text-danger">*</span>')->for('skill')->class('required-label') }}
                            {{ html()->select('skill') 
                                                ->class('form-control')->placeholder('Select Skill')
                                                ->required() ->value(old('skill',$employee->skill_type)) 
                                                ->options($skills->pluck('skill_level', 'skill_level')->toArray()) 
                                            }}
                            @error('skill')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 my-3 form-group">
                            {{ html()->label('Role <span class="text-danger">*</span>')->for('role_id')->class('required-label') }}
                            {{ html()->select('role_id', $roles->pluck('name', 'id'))
                            ->class('form-control')
                            ->required()
                            ->placeholder('Select Role')
                            ->value(old('role_id', $employee->role_id)) }}
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3 form-group">
                            {{ html()->label('ESIC No')->for('esic_no') }}
                            {{ html()->text('esic_no')
                            ->class('form-control')
                            ->placeholder('ESIC No')
                            ->value(old('esic_no', $employee->employeedetail->esic_no)) }}
                            @error('esic_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3 form-group">
                            {{ html()->label('PF No')->for('pf_no') }}
                            {{ html()->text('pf_no')
                                                ->class('form-control')
                                                ->placeholder('PF No')
                                                ->value(old('pf_no', $employee->employeedetail->epf_no)) }}
                            @error('pf_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3 form-group">
                            {{ html()->label('Date of Birth <span class="text-danger">*</span>')->for('date_of_birth')->class('required-label') }}
                            {{ html()->date('date_of_birth')
                            ->class('form-control')
                            ->required()
                            ->value(old('date_of_birth', $employee->date_of_birth)) }}
                        
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3 form-group">
                            {{ html()->label('Date of Joining <span class="text-danger">*</span>')->for('date_of_joining')->class('required-label') }}
                            {{ html()->date('date_of_joining')
                            ->class('form-control')
                            ->required()
                            ->value(old('date_of_joining', $employee->employeedetail->date_of_joining)) }}
                            @error('date_of_joining')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-header bg-transparent border-primary">
                        <h5 class="my-0 text-primary">Address Details</h5>
                    </div>

                    <!-- State, District, City -->
                    <div class="row">
                        <div class="col-md-3 my-3 form-group">
                            {{ html()->label('State <span class="text-danger">*</span>')->for('state')->class('required-label') }}
                            {{ html()->select('state', $states, old('state_id', $employee->employeedetail->state_id ?? ''))
                                                ->class('form-control')
                                                ->required()
                                                ->placeholder('Select State') }}
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 my-3 form-group">
                            {{ html()->label('District <span class="text-danger">*</span>')->for('distt')->class('required-label') }}
                            {{ html()->select('distt',  ['' => 'Select District'] + $district, old('district_id', $employee->employeedetail->distt_id ?? ''))
                                                ->class('form-control')
                                                ->required()
                                                ->attribute('id', 'district') }}
                            @error('distt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 my-3 form-group">
                            {{ html()->label('City <span class="text-danger">*</span>')->for('city')->class('required-label') }}
                            {{ html()->select('city', ['' => 'Select City']+ $city, old('city_id', $employee->employeedetail->city_id ?? ''))
                                                ->class('form-control')
                                                ->required()
                                                ->attribute('id', 'city') }}
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 my-3 form-group">
                            {{ html()->label('Location <span class="text-danger">*</span>')->for('location')->class('required-label') }}
                            {{ html()->text('location')
                                                ->class('form-control')
                                                ->required()
                                                ->placeholder('Location')
                                                ->value(old('location', $employee->employeedetail->location)) }}
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    
                     <!-- Submit Button -->
                    <div class="row">
                        <div class="col-md-12 my-3 form-group text-center">
                            {{ html()->submit('Update')->class('btn btn-primary') }}
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
</div>
</div>



@endsection




@push('scripts')
<script src="{{asset('admin-assets/libs/dropify/js/dropify.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin-assets/libs/dropify/dropify.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#state').change(function() {
            var stateId = $(this).val();
            if (stateId) {
                $.ajax({
                    url: '/get-districts/' + stateId,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#district').empty();
                        $('#district').append('<option value="">Select District</option>');
                        $.each(data, function(key, value) {
                            $('#district').append('<option value="' + key + '">' + value + '</option>');
                        });
                        // Clear city options when state changes
                        $('#city').empty();
                        $('#city').append('<option value="">Select City</option>');
                    }
                });
            } else {
                $('#district').empty();
                $('#district').append('<option value="">Select District</option>');
                $('#city').empty();
                $('#city').append('<option value="">Select City</option>');
            }
        });
    
        $('#district').change(function() {
            var districtId = $(this).val();
            if (districtId) {
                $.ajax({
                    url: '/get-cities/' + districtId,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#city').empty();
                        $('#city').append('<option value="">Select City</option>');
                        $.each(data, function(key, value) {
                            $('#city').append('<option value="' + key + '">' + value + '</option>');
                        });
                    }
                });
            } else {
                $('#city').empty();
                $('#city').append('<option value="">Select City</option>');
            }
        });
    });
    </script>
    <script>
        $(document).ready(function() {
            $('#company_id').change(function() {
                var companyId = $(this).val();
                var departmentSelect = $('#department_id');
    
                // Clear existing department options
                departmentSelect.empty().append('<option value="">Select Department</option>');
    
                if (companyId) {
                    $.ajax({
                        url: '{{ route("admin.get-departments-by-company") }}', // Ensure this route is defined
                        type: 'GET',
                        data: {
                            company_id: companyId
                        },
                        success: function(response) {
                            if (response.departments && response.departments.length > 0) {
                                $.each(response.departments, function(key, department) {
                                    departmentSelect.append('<option value="'+ department.id +'">'+ department.name +'</option>');
                                });
                            } else {
                                departmentSelect.append('<option value="">No Departments Available</option>');
                            }
                        },
                        error: function() {
                            departmentSelect.append('<option value="">Error loading departments</option>');
                        }
                    });
                }
            });
    
            // Trigger change on page load to pre-select department if company is already selected
            // $('#company_id').trigger('change');
        });
    </script>
@endpush