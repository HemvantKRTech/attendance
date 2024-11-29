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

                {{ html()->form('PUT', route('admin.'.request()->segment(2).'.update', $company->id))->attribute('files', true)->open() }}

                <div class="row my-1">
                    <div class="col-lg-12 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-header bg-transparent border-primary">
                                    <h5 class="my-0 text-primary">Company Information</h5>
                                </div>
                                <div class="card-body">
                
                                    <!-- Existing Fields -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('Company Name')->for('company_name') }}<span class="text-danger">*</span>
                                            {{ html()->text('company_name')
                                                ->class('form-control')
                                                ->required()
                                                ->placeholder('Company Name')
                                                ->value(old('company_name', $company->name)) }}
                                            @error('company_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('Email')->for('email') }}<span class="text-danger">*</span>
                                            {{ html()->email('email')
                                                ->class('form-control')
                                                ->required()
                                                ->placeholder('Email')
                                                ->value(old('email', $company->email)) }}
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @php
    $companyTypes = [
    'Private Limited Company (Pvt. Ltd.)',
    'Public Limited Company (Ltd.)',
    'One Person Company (OPC)',
    'Partnership Firm',
    'Limited Liability Partnership (LLP)',
    'Sole Proprietorship',
    'Section 8 Company (Non-Profit Organization)',
    'Joint Venture Company',
    'Public Sector Undertaking (PSU) or Government Company',
    'Holding and Subsidiary Companies',
];

@endphp
                                    <div class="row">
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('Type')->for('type') }}<span class="text-danger">*</span>
        {{ html()->select('type', array_combine($companyTypes, $companyTypes), old('type', $company->details->type ?? ''))
            ->class('form-control')
            ->required() ->id('company-type')
            ->placeholder('Select Company Type') }}
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('Owner Name')->for('owner_name') }}<span class="text-danger">*</span>
                                            {{ html()->text('owner_name')
                                                ->class('form-control')
                                                ->required()
                                                ->placeholder('Owner Name')
                                                ->value(old('owner_name', $company->owner_name ?? '')) }}
                                            @error('owner_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                
                                    <div class="row">
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('Contact No.')->for('contact_no') }}<span class="text-danger">*</span>
                                            {{ html()->text('contact_no')
                                                ->class('form-control')
                                                ->required()
                                                ->placeholder('Contact No.')
                                                ->value(old('contact_no', $company->mobile ?? '')) }}
                                            @error('contact_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('State')->for('state_id') }}<span class="text-danger">*</span>
                                            {{ html()->select('state', $states, old('state_id', $company->details->state_id ?? ''))
                                                ->class('form-control')
                                                ->required()
                                                ->placeholder('Select State') }}
                                            @error('state_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        
                                    </div>
                
                                    <div class="row">
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('District')->for('district_id') }}<span class="text-danger">*</span>
                                            {{ html()->select('distt',  ['' => 'Select District'] + $district, old('district_id', $company->details->distt_id ?? ''))
                                                ->class('form-control')
                                                ->required()
                                                ->attribute('id', 'district') }}
                                            @error('distt')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('City')->for('city') }}<span class="text-danger">*</span>
                                            {{ html()->select('city', ['' => 'Select City']+ $city, old('city_id', $company->details->city_id ?? ''))
                                                ->class('form-control')
                                                ->required()
                                                ->attribute('id', 'city') }}
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                    </div>
                
                                    <div class="row">
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('Address')->for('address') }}<span class="text-danger">*</span>
                                            {{ html()->textarea('address')
                                                ->class('form-control')
                                                ->required()
                                                ->placeholder('Address')
                                                ->rows(2)
                                                ->value(old('address', $company->details->address ?? '')) }}
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('GST No.')->for('gst_no') }}<span class="text-danger">*</span>
                                            {{ html()->text('gst_no')
                                                ->class('form-control')
                                                ->required()
                                                ->placeholder('GST No.')
                                                ->value(old('gst_no', $company->details->gst_no ?? '')) }}
                                            @error('gst_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('PAN No.')->for('pan_no') }}<span class="text-danger">*</span>
                                            {{ html()->text('pan_no')->class('form-control')->required()->placeholder('PAN No.')->value(old('pan_no',$company->details->pan_no ?? '')) }}
                                            @error('pan_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
            
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('Aadhar No.')->for('aadhar_no') }}<span class="text-danger">*</span>
                                            {{ html()->text('aadhar_no')->class('form-control')->required()->placeholder('Aadhar No.')->value(old('aadhar_no',$company->details->aadhar_no ?? '')) }}
                                            @error('aadhar_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
            
                                    <div class="row">
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('Udyam No.')->for('udyam_no') }}<span class="text-danger">*</span>
                                            {{ html()->text('udyam_no')->class('form-control')->placeholder('Enter your 19 digit Udyam Registration number')->value(old('udyam_no',$company->details->udyam_no ?? '')) }}
                                            @error('udyam_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
            
                                        <div id="cin-field" class="col-md-6 mb-3 form-group" style="display: none;">
                                            {{ html()->label('CIN No.')->for('cin_no') }}<span class="text-danger">*</span>
                                            {{ html()->text('cin_no')
                                                ->class('form-control')
                                                ->placeholder('Enter your 21 digit CIN No.')
                                                ->value(old('cin_no', $company->details->cin_no ?? '')) }}
                                            @error('cin_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
            
                                    <div class="row">
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('EPF No.')->for('epf_no') }}<span class="text-danger">*</span>
                                            {{ html()->text('epf_no')->class('form-control')->placeholder('Enter ypur 15 digit EPF No.')->value(old('epf_no',$company->details->epf_no ?? '')) }}
                                            @error('epf_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
            
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('ESIC No.')->for('esic_no') }}<span class="text-danger">*</span>
                                            {{ html()->text('esic_no')->class('form-control')->placeholder('Enter ypur 17 digit ESIC No.')->value(old('esic_no',$company->details->esic_no ?? '')) }}
                                            @error('esic_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- Other fields -->
                
                                    <div class="row">
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('Bank Name')->for('bank_name') }}<span class="text-danger">*</span>
                                            {{ html()->text('bank_name')
                                                ->class('form-control')
                                                ->required()
                                                ->placeholder('Bank Name')
                                                ->value(old('bank_name', $company->details->bank_name ?? '')) }}
                                            @error('bank_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('Account No.')->for('ac_no') }}<span class="text-danger">*</span>
                                            {{ html()->text('ac_no')
                                                ->class('form-control')
                                                ->required()
                                                ->placeholder('Account No.')
                                                ->value(old('ac_no', $company->details->ac_no ?? '')) }}
                                            @error('ac_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                
                                    <div class="row">
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('IFS Code')->for('ifs_code') }}<span class="text-danger">*</span>
                                            {{ html()->text('ifs_code')
                                                ->class('form-control')
                                                ->required()
                                                ->placeholder('IFS Code')
                                                ->value(old('ifs_code', $company->details->ifs_code ?? '')) }}
                                            @error('ifs_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
               
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->submit('Update Company')->class('btn btn-soft-secondary waves-effect waves-light') }}
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
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        // Function to show/hide CIN No. field based on company type
        function toggleCinField() {
            var companyType = document.getElementById('company-type').value;
            var cinField = document.getElementById('cin-field');
            
            if (companyType === 'Limited Liability Partnership (LLP)') {
                cinField.style.display = 'none'; // Hide CIN No. field
            } else {
                cinField.style.display = 'block'; // Show CIN No. field
            }
        }

        // Run the function on page load
        toggleCinField();

        // Run the function when the company type changes
        document.getElementById('company-type').addEventListener('change', function() {
            toggleCinField();
        });
    });
</script>

@endpush