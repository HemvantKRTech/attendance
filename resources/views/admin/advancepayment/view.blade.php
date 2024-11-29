@extends('admin.layouts.master')

@push('links')
<!-- Add any additional CSS or links here if needed -->
@endpush

@section('main')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $company->name }} Employees</h4>
            @can('add_department')
            <div class="page-title-right">
                {{-- <a href="{{ route('admin.department.create') }}" class="btn-sm btn btn-primary btn-label rounded-pill">
                    <i class="bx bx-plus label-icon align-middle rounded-pill fs-16 me-2"></i>
                </a> --}}
            </div>
            @endcan
        </div>
    </div>
</div>
<!-- end page title -->
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.'.request()->segment(2).'.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                    
                    <h5>Employees:</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Employee Code</th>
                                <th>Employee Name</th>
                                <th>Advance Amount</th>
                                <th>Payment Type</th>
                                <th>Annual Interest Rate (%)</th>
                                <th>Total EMI Count</th>
                                <th>EMI Amount</th>
                                <th>Total Payment Amount</th> <!-- New column for Total Payment Amount -->
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                            <tr>
                                <td>{{ $employee->employeedetail->employee_code }}</td>
                                <td>{{ $employee->name }}</td>
                
                                <!-- Advance Amount Input -->
                                <td>
                                    <input type="number" name="payment[{{ $employee->id }}][amount]" class="form-control" placeholder="Advance Amount" min="0"
                                           value="{{ old("payment.{$employee->id}.amount") }}">
                                </td>
                
                                <!-- Payment Type Selection -->
                                <td>
                                    <select name="payment[{{ $employee->id }}][payment_type]" class="form-select" onchange="toggleEMIFields(this, {{ $employee->id }})">
                                        <option value="">Select Payment Type</option>
                                        <option value="monthly" {{ old("payment.{$employee->id}.payment_type") == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="emi" {{ old("payment.{$employee->id}.payment_type") == 'emi' ? 'selected' : '' }}>EMI</option>
                                    </select>
                                </td>

                                <!-- Annual Interest Rate Input (shown if "EMI" is selected) -->
                                <td>
                                    <input type="number" name="payment[{{ $employee->id }}][interest_rate]" class="form-control" placeholder="Annual Interest Rate (%)" min="0" step="0.01"
                                           value="{{ old("payment.{$employee->id}.interest_rate") }}"
                                           style="display: {{ old("payment.{$employee->id}.payment_type") == 'emi' ? 'block' : 'none' }};"
                                           onchange="calculateEMI({{ $employee->id }})">
                                </td>
                                
                                <!-- Total EMI Count Input (shown if "EMI" is selected) -->
                                <td>
                                    <input type="number" name="payment[{{ $employee->id }}][total_emi_count]" class="form-control" placeholder="Total EMI Count" min="0"
                                           value="{{ old("payment.{$employee->id}.total_emi_count") }}"
                                           style="display: {{ old("payment.{$employee->id}.payment_type") == 'emi' ? 'block' : 'none' }};"
                                           onchange="calculateEMI({{ $employee->id }})">
                                </td>

                                <!-- EMI Amount Input (shown if "EMI" is selected) -->
                                <td>
                                    <input type="number" name="payment[{{ $employee->id }}][emi_amount]" class="form-control" placeholder="EMI Amount" min="0"
                                           value="{{ old("payment.{$employee->id}.emi_amount") }}"
                                           readonly>
                                </td>
                                
                                <!-- Total Payment Amount Input (shown if "EMI" is selected) -->
                                <td>
                                    <input type="number" name="payment[{{ $employee->id }}][total_payment_amount]" class="form-control" placeholder="Total Payment Amount" min="0"
                                           value="{{ old("payment.{$employee->id}.total_payment_amount") }}"
                                           readonly>
                                </td>
                
                                <!-- Remarks Input -->
                                <td>
                                    <input type="text" name="payment[{{ $employee->id }}][remarks]" class="form-control" placeholder="Remarks"
                                           value="{{ old("payment.{$employee->id}.remarks") }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Submit Advance Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!--end col-->
</div><!--end row-->

@endsection

@push('scripts')
<!-- Add any additional JS here if needed -->
<script>
    function toggleEMIFields(paymentTypeSelect, employeeId) {
        const paymentType = paymentTypeSelect.value;
        const interestRateInput = document.querySelector(`input[name="payment[${employeeId}][interest_rate]"]`);
        const totalEmiCountInput = document.querySelector(`input[name="payment[${employeeId}][total_emi_count]"]`);
        const emiAmountInput = document.querySelector(`input[name="payment[${employeeId}][emi_amount]"]`);
        const totalPaymentInput = document.querySelector(`input[name="payment[${employeeId}][total_payment_amount]"]`);

        if (paymentType === 'emi') {
            interestRateInput.style.display = 'block';
            totalEmiCountInput.style.display = 'block';
        } else {
            interestRateInput.style.display = 'none';
            totalEmiCountInput.style.display = 'none';
            emiAmountInput.value = ''; // Clear EMI Amount when switching to Monthly
            totalPaymentInput.value = ''; // Clear Total Payment Amount
        }
    }

    function calculateEMI(employeeId) {
    const amount = parseInt(document.querySelector(`input[name="payment[${employeeId}][amount]"]`).value) || 0; // Advance amount
    const interestRate = parseFloat(document.querySelector(`input[name="payment[${employeeId}][interest_rate]"]`).value) || 0; // Annual interest rate
    const totalEMICount = parseInt(document.querySelector(`input[name="payment[${employeeId}][total_emi_count]"]`).value) || 0; // Total EMI count

    const emiAmountInput = document.querySelector(`input[name="payment[${employeeId}][emi_amount]"]`);
    const totalPaymentInput = document.querySelector(`input[name="payment[${employeeId}][total_payment_amount]"]`);

    if (totalEMICount > 0) {
        if (interestRate > 0) {
            // Monthly interest rate
            const monthlyInterestRate = (interestRate / 12) / 100;

            // EMI formula
            const emi = Math.round((amount * monthlyInterestRate * Math.pow(1 + monthlyInterestRate, totalEMICount)) / 
                        (Math.pow(1 + monthlyInterestRate, totalEMICount) - 1));

            // Set EMI in the input field
            emiAmountInput.value = emi; // Round to nearest whole number

            // Calculate total payment amount
            const totalPaymentAmount = Math.round(emi * totalEMICount); // Round to nearest whole number
            totalPaymentInput.value = totalPaymentAmount;
        } else {
            // If interest rate is 0
            const emi = Math.round(amount / totalEMICount); // Simple division for EMI
            emiAmountInput.value = emi; // Set EMI
            const totalPaymentAmount = amount; // Total payment is just the advance amount
            totalPaymentInput.value = totalPaymentAmount; // Total payment is the same as the amount
        }
    } else {
        emiAmountInput.value = ''; // Clear EMI if inputs are invalid
        totalPaymentInput.value = ''; // Clear Total Payment Amount if inputs are invalid
    }
}

</script>
@endpush
