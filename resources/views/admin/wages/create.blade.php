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

                {{ html()->form('POST', route('admin.'.request()->segment(2).'.store'))->class('form-horizontal')->attribute('id', 'wageform')->open() }}

                <div class="row my-1">
                    <div class="col-lg-12 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-header bg-transparent border-primary">
                                    <h5 class="my-0 text-primary">Wage Management</h5>
                                </div>
                                <div class="card-body">
            
                                    <!-- Wage for UNSKILLED Workers -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('Unskilled Worker Wage')->for('unskilled_wage') }}
                                            {{ html()->text('unskilled_wage')->class('form-control')->required()->placeholder('Enter wage for unskilled workers')->value(old('unskilled_wage')) }}
                                            @error('unskilled_wage')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
            
                                    <!-- Wage for SEMI-SKILLED Workers -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('Semi-Skilled Worker Wage')->for('semi_skilled_wage') }}
                                            {{ html()->text('semi_skilled_wage')->class('form-control')->required()->placeholder('Enter wage for semi-skilled workers')->value(old('semi_skilled_wage')) }}
                                            @error('semi_skilled_wage')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
            
                                    <!-- Wage for SKILLED Workers -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->label('Skilled Worker Wage')->for('skilled_wage') }}
                                            {{ html()->text('skilled_wage')->class('form-control')->required()->placeholder('Enter wage for skilled workers')->value(old('skilled_wage')) }}
                                            @error('skilled_wage')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
            
                                    <!-- Save Button -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3 form-group">
                                            {{ html()->submit('Save')->class('btn btn-primary') }}
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
@endpush