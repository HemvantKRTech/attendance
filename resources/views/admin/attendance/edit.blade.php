@extends('admin.layouts.master')

@section('main')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4>Mark Attendance for {{ $company->name }}</h4>

                <form action="{{ route('admin.mark-attendance.updatestore') }}" method="POST">
                    @csrf
                    @method('PUT') <!-- Override to use PUT request -->
                    
                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                    
                    <!-- Date and Employee Attendance Inputs -->
                    <div class="mb-4">
                        <label for="attendance_date" class="form-label">Select Date</label>
                        <input type="date" name="attendance_date" class="form-control" value="{{ old('attendance_date') }}" max="{{ date('Y-m-d') }}" required>
                    </div>
                
                    <h5>Employees:</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Employee Code</th>
                                <th>Employee Name</th>
                                <th>Status</th>
                                <th>Working Hour</th>
                                <th>Overtime</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                            <tr>
                                <td>{{ $employee->employeedetail->employee_code }}</td>
                                <td>{{ $employee->name }}</td>
                                <td>
                                    <select name="attendance[{{ $employee->id }}][status]" class="form-select" required>
                                        <option value="present" {{ old("attendance.{$employee->id}.status") == 'present' ? 'selected' : '' }}>Present</option>
                                        <option value="absent" {{ old("attendance.{$employee->id}.status") == 'absent' ? 'selected' : '' }}>Absent</option>
                                        <option value="hours" {{ old("attendance.{$employee->id}.status") == 'hours' ? 'selected' : '' }}>Hours Worked</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="attendance[{{ $employee->id }}][working_hours]" class="form-control" placeholder="Working Hours" min="0" value="{{ old("attendance.{$employee->id}.working_hours") }}">
                                </td>
                                <td>
                                    <input type="number" name="attendance[{{ $employee->id }}][overtime]" class="form-control" placeholder="Overtime" min="0" step="any" value="{{ old("attendance.{$employee->id}.overtime") }}">
                                </td>
                                <td>
                                    <input type="text" name="attendance[{{ $employee->id }}][remarks]" class="form-control" placeholder="Remarks" value="{{ old("attendance.{$employee->id}.remarks") }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Submit Attendance</button>
                    </div>
                </form>
                

            </div>
        </div>
    </div><!--end col-->
</div><!--end row-->

@endsection

@push('scripts')
<script>
    document.querySelectorAll('select[name*="status"]').forEach(function(select) {
        select.addEventListener('change', function() {
            const row = this.closest('tr');
            const workingHoursInput = row.querySelector('input[name*="working_hours"]');
            const remarksInput = row.querySelector('input[name*="remarks"]');

            if (this.value === 'hours') {
                workingHoursInput.style.display = 'block';
                // remarksInput.style.display = 'block';
            } else {
                workingHoursInput.style.display = 'none';
                // remarksInput.style.display = 'none';
            }
        });
    });
</script>
@endpush
