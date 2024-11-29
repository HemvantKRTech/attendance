@extends('admin.layouts.master')
@push('links')

@endpush

@section('main')

<style>
    body {
    overflow: auto; 
}
.card {
    overflow: visible; 
}
.card-body {
    max-height: 400px; 
    overflow-y: auto; 
}

</style>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ Str::title(str_replace('-', ' ', request()->segment(2))) }}</h4>
            @can('add_admin')
            <div class="page-title-right">
                <a href="{{ route('admin.'.request()->segment(2).'.index') }}" class="btn-sm btn btn-primary btn-label rounded-pill">
                    <i class="bx bx-plus label-icon align-middle rounded-pill fs-16 me-2"></i>
                    Upload {{ Str::title(str_replace('-', ' ', request()->segment(2))) }} Sheet
                </a>
            </div>
            @endcan
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="mb-3">
            <a href="{{ route('admin.salary.export', ['company' => $salaryDetails->first()->company_id, 'month' => $salaryDetails->first()->month, 'year' => $salaryDetails->first()->year]) }}" class="btn btn-primary">
                Export to Excel
            </a>
            
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Year</th>
                            <th>Month</th>
                            <th>Working Days</th>
                            <th>Basic</th>
                            <th>PF Basic</th>
                            <th>HRA</th>
                            <th>Conveyance</th>
                            <th>Other Allowance</th>
                            <th>Rate Of Pay</th>
                            <th>EPF (Employee)</th>
                            <th>EPF (Employer)</th>
                            <th>EPS (Employer)</th>
                            <th>ESI (Employee)</th>
                            <th>ESI (Employer)</th>
                            <th>Total Deductions</th>
                            <th>Net Payable</th>
                            <th>Advance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salaryDetails as $detail)
                        <tr>
                            <td>{{ $detail->employee->name }}</td>
                            <td>{{ $detail->year }}</td>
                            <td>{{ $detail->month }}</td>
                            <td>{{ $detail->working_days }}</td>
                            <td>{{ number_format($detail->basic, 2) }}</td>
                            <td>{{ number_format($detail->pf_basic, 2) }}</td>
                            <td>{{ number_format($detail->hra, 2) }}</td>
                            <td>{{ number_format($detail->conveyance, 2) }}</td>
                            <td>{{ number_format($detail->other_allowance, 2) }}</td>
                            <td>{{ number_format($detail->rate_of_pay , 2) }}</td>
                            <td>{{ number_format($detail->epf_employee, 2) }}</td>
                            <td>{{ number_format($detail->epf_employer, 2) }}</td>
                            <td>{{ number_format($detail->eps_employer, 2) }}</td>
                            <td>{{ number_format($detail->esi_employee, 2) }}</td>
                            <td>{{ number_format($detail->esi_employer, 2) }}</td>
                            <td>{{ number_format($detail->total_deductions, 2) }}</td>
                            <td>{{ number_format($detail->net_payable, 2) }}</td>
                            <td>{{ number_format($detail->advance, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div><!--end col-->
</div><!--end row-->


@endsection

@push('scripts')

@endpush
