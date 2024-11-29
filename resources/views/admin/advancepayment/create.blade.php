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
                @can('add_salary')
                <div class="page-title-right">
                    {{-- <a href="{{ route('admin.' . request()->segment(2) . '.allsalary') }}" class="btn-sm btn btn-primary btn-label rounded-pill">
                        <i class="bx bx-plus label-icon align-middle rounded-pill fs-16 me-2"></i>
                        Export Company {{ Str::title(str_replace('-', ' ', request()->segment(2))) }} Sheet
                    </a> --}}
                </div>
                @endcan

                <div class="row my-1">
                    <div class="col-lg-12 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-header bg-transparent border-primary">
                                <h5 class="my-0 text-primary">Add Advance Payment </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department Form -->
                {{ html()->form('POST', route('admin.'.request()->segment(2).'.store'))
    ->class('form-horizontal')
    ->attribute('id', 'advancePaymentForm')
    ->open() }}

<div class="row my-1">
    <div class="col-lg-12 col-sm-12 col-12">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <!-- Company Selection -->
                    <div class="row mb-3">
                        <div class="col-md-6 form-group">
                            {{ html()->label('Select Company')->for('company_id') }}
                            {{ html()->select('company_id', $companies->pluck('name', 'id'))
                                ->class('form-control')
                                ->required()
                                ->placeholder('Select Company')
                                ->value(old('company_id'))
                                ->attribute('onchange', 'fetchEmployees(this.value)') }}
                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Employee Selection -->
                        <div class="col-md-6 form-group">
                            {{ html()->label('Select Employee')->for('employee_id') }}
                            {{ html()->select('employee_id', [])
                                ->class('form-control')
                                ->required()
                                ->placeholder('Select Employee')
                                ->value(old('employee_id'))
                                ->attribute('id', 'employee_id') }} <!-- Dynamic select -->
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Advance Amount -->
                    <div class="row mb-3">
                        <div class="col-md-6 form-group">
                            {{ html()->label('Advance Amount')->for('amount') }}
                            {{ html()->text('amount')
                                ->class('form-control')
                                ->required()
                                ->placeholder('Enter Advance Amount')
                                ->value(old('amount')) }}
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Type Selection -->
                        <div class="col-md-6 form-group">
                            {{ html()->label('Payment Type')->for('payment_type') }}
                            {{ html()->select('payment_type', ['monthly' => 'Monthly', 'emi' => 'EMI'])
                                ->class('form-control')
                                ->required()
                                ->placeholder('Select Payment Type')
                                ->value(old('payment_type'))
                                ->attribute('onchange', 'toggleEMIFields(this.value)') }}
                            @error('payment_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- EMI Fields -->
                    <div class="row mb-3">
                        <div class="col-md-6 form-group" id="emiFields" style="display: none;">
                            {{ html()->label('EMI Amount')->for('emi_amount') }}
                            {{ html()->text('emi_amount')
                                ->class('form-control')
                                ->placeholder('Enter EMI Amount')
                                ->value(old('emi_amount')) }}
                            @error('emi_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group" id="emiCountField" style="display: none;">
                            {{ html()->label('Total EMI Count')->for('total_emi_count') }}
                            {{ html()->number('total_emi_count')
                                ->class('form-control')
                                ->placeholder('Enter Total EMI Count')
                                ->value(old('total_emi_count')) }}
                            @error('total_emi_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="row mb-3">
                        <div class="col-md-12 form-group">
                            {{ html()->label('Description (Optional)')->for('description') }}
                            {{ html()->textarea('description')
                                ->class('form-control')
                                ->placeholder('Enter Description')
                                ->value(old('description')) }}
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row">
                        <div class="col-md-12 mb-3 form-group text-end">
                            {{ html()->submit('Add Advance Payment')->class('btn btn-primary') }}
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
<!-- You can include any scripts here if needed -->
<script>
    function fetchEmployees(companyId) {
        if (companyId) {
            $.ajax({
                url: '{{ route('admin.getEmployees') }}', // Your route for fetching employees
                type: 'GET',
                data: { company_id: companyId },
                success: function(data) {
                    var employeeSelect = $('#employee_id');
                    employeeSelect.empty();
                    employeeSelect.append('<option value="">Select Employee</option>');

                    $.each(data, function(key, value) {
                        employeeSelect.append('<option value="'+ key +'">'+ value +'</option>');
                    });
                },
                error: function() {
                    alert('Error fetching employees!');
                }
            });
        }
    }
</script>
<script>
    function toggleEMIFields(paymentType) {
        if (paymentType === 'emi') {
            document.getElementById('emiFields').style.display = 'block';
            document.getElementById('emiCountField').style.display = 'block';
        } else {
            document.getElementById('emiFields').style.display = 'none';
            document.getElementById('emiCountField').style.display = 'none';
        }
    }
</script>

@endpush
