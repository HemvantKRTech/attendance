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

<!-- Form Starts Here -->
{{ html()->form('POST', route('admin.'.request()->segment(2).'.import'))
    ->class('form-horizontal')
    ->attribute('id', 'wageform')
    ->attribute('enctype', 'multipart/form-data')
    ->open() }}

    <div class="row my-1">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">

                        <!-- Company Selection, Excel File Upload, and Submit Button in One Row -->
                        <div class="row">

                            <!-- Company Dropdown -->
                            <div class="col-md-4 mb-3 form-group">
                                {{ html()->label('Select Company')->for('company_id') }}
                                <select name="company_id" id="company_id" class="form-control" required>
                                    <option value="" selected disabled>Select Company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Excel File Upload -->
                            <div class="col-md-4 mb-3 form-group">
                                {{ html()->label('Upload Company Detail Excel Sheet')->for('company_excel') }}
                                {{ html()->file('company_excel')
                                    ->class('form-control')
                                    ->required()
                                    ->accept('.xlsx, .xls, .csv') }}
                                @error('company_excel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="col-md-4 mb-3 form-group d-flex align-items-end justify-content-end">
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
</div>
</div>



@endsection




@push('scripts')
@endpush