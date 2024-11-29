@extends('admin.layouts.master')
@push('links')

@endpush

@section('main')

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
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Select Month</th>
                            <th>Select Year</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companies as $id => $name)
                        <tr>
                            <td>{{ $name }}</td>
                            <td>
                                <select name="month[{{ $id }}]" class="form-control" id="month_{{ $id }}">
                                    <option value="">Select Month</option>
                                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                        <option value="{{ $month }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="year[{{ $id }}]" class="form-control" id="year_{{ $id }}">
                                    <option value="">Select Year</option>
                                    @php
                                        $currentYear = date('Y');
                                        $years = range($currentYear - 5, $currentYear);
                                    @endphp
                                    @foreach($years as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                {{-- <form method="POST" action="{{ route('admin.salary.export', $id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Export</button>
                                </form> --}}
                                <a href="#" class="btn btn-info" onclick="return validateAndRedirect({{ $id }});">
                                    View
                                </a>
                                
                            </td>
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

<script>
    function validateAndRedirect(companyId) {
        const monthSelect = document.querySelector(`select[name="month[${companyId}]"]`);
        const yearSelect = document.querySelector(`select[name="year[${companyId}]"]`);

        const selectedMonth = monthSelect.value;
        const selectedYear = yearSelect.value;

        // Check if month and year are selected
        if (!selectedMonth || !selectedYear) {
            alert('Please select both month and year before viewing salary details.');
            return false; // Stop redirect
        } else {
            // Redirect to the view salary page with selected month and year
            window.location.href = `{{ url('admin/salary/view') }}/${companyId}?month=${selectedMonth}&year=${selectedYear}`;
            return false; // Prevent default anchor behavior
        }
    }
</script>


@endpush
