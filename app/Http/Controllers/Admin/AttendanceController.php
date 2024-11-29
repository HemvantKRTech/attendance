<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Http\Resources\Admin\Attendance\AttendanceCollection;
use Carbon\Carbon;
use App\Http\Resources\Admin\Company\CompanyCollection;
use App\Models\Employee;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index(Request $request){
        if ($request->wantsJson()) {
       
            $datas = Company::orderBy('admins.created_at', 'desc')
        ->where('admins.role_id', 3) 
        ->whereNotIn('admins.id', [1]) 
        ->join('roles', 'roles.id', '=', 'admins.role_id') 
        ->select([
            'admins.id as id', 
            'roles.name as role', 
            'admins.name as name', 
            'admins.mobile as mobile',
            'email', 
            'admins.status'
        ]);
    
            $request->merge(['recordsTotal' => $datas->count(), 'length' => $request->length]);
            $datas = $datas->limit($request->length)->offset($request->start)->get();
            return response()->json(new CompanyCollection($datas));
            // dd($datas);
        }
        
        return view('admin.attendance.list');
    }
    public function show($id)
    {
        $company = Company::findOrFail($id);
        $employees = Employee::where('company_id', $id)->with('employeedetail')->get();
    
        // Define the start and end dates of the current month
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
    
        // Fetch unique attendance dates within the current month
        $attendanceDates = Attendance::where('company_id', $id)
        ->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
        ->select('attendance_date')
        ->distinct()
        ->orderBy('attendance_date', 'asc')
        ->get()
        ->map(function($attendance) {
            return Carbon::parse($attendance->attendance_date)->format('d-m-Y');
        });
        // dd($attendanceDates);
        
        return view('admin.attendance.view', compact('company', 'employees', 'attendanceDates'));
    }
    public function edit($id){
        // dd($id);
        $company = Company::findOrFail($id);
        $employees =Employee::where('company_id',$id)->with('employeedetail')->get();
        // dd($employees );
        return view('admin.attendance.edit',compact('company','employees'));
    }
    public function store(Request $request)
{
    try {
        // Validate the incoming request data
        $validated = $request->validate([
            'company_id' => 'required|exists:admins,id', // Check if the company ID exists in the companies table
            'attendance_date' => 'required|date',
            'attendance.*.status' => 'required|in:present,absent,hours',
            'attendance.*.working_hours' => 'nullable|required_if:attendance.*.status,hours|numeric|min:0',
            'attendance.*.overtime' => 'nullable|numeric|min:0',
            'attendance.*.remarks' => 'nullable',
        ]);

        $attendanceDate = $request->attendance_date;

        // Loop through each employee's attendance data
        foreach ($request->attendance as $employeeId => $data) {
            // Check if attendance already exists for this employee on the selected date
            $employeeAttendanceExists = Attendance::where('company_id', $request->company_id)
                ->where('attendance_date', $attendanceDate)
                ->where('employee_id', $employeeId)
                ->exists();

            // If attendance does not exist, create a new record for the employee
            if (!$employeeAttendanceExists) {
                Attendance::create([
                    'company_id' => $request->company_id,
                    'employee_id' => $employeeId,
                    'status' => $data['status'],
                    'working_hours' => ($data['status'] == 'hours') ? ($data['working_hours'] ?? 0) : null,
                    'overtime' => $data['overtime'] ?? 0,
                    'remarks' => ($data['status'] == 'hours') ? ($data['remarks'] ?? '') : null,
                    'attendance_date' => $attendanceDate,
                ]);
            }
        }

        // Redirect back with a success message
        return redirect()->back()->with(['class' => 'success', 'message' => 'Attendance Marked successfully.']);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation errors
        return redirect()->back()->withErrors($e->validator)->withInput();
    } catch (\Exception $e) {
        // Handle general exceptions
        return redirect()->back()->with(['class' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
}
////update store

public function updatestore(Request $request)
{
    // dd($request->all());
    try {
        // Validate the incoming request data
        $validated = $request->validate([
            'company_id' => 'required|exists:admins,id', // Check if the company ID exists in the companies table
            'attendance_date' => 'required|date',
            'attendance.*.status' => 'required|in:present,absent,hours',
            'attendance.*.working_hours' => 'nullable|required_if:attendance.*.status,hours|numeric|min:0',
            'attendance.*.overtime' => 'nullable|numeric|min:0',
            'attendance.*.remarks' => 'nullable',
        ]);
    
        $attendanceDate = $request->attendance_date;
    
        // Loop through each employee's attendance data
        foreach ($request->attendance as $employeeId => $data) {
            // Use updateOrCreate to update existing attendance or create a new one if it doesn't exist
            Attendance::updateOrCreate(
                [
                    'company_id' => $request->company_id,
                    'employee_id' => $employeeId,
                    'attendance_date' => $attendanceDate,
                ],
                [
                    'status' => $data['status'],
                    'working_hours' => ($data['status'] == 'hours') ? ($data['working_hours'] ?? 0) : null,
                    'overtime' => $data['overtime'] ?? 0,
                    'remarks' => ($data['status'] == 'hours') ? ($data['remarks'] ?? '') : null,
                ]
            );
        }
    
        // Redirect back with a success message
        return redirect()->back()->with(['class' => 'success', 'message' => 'Attendance records have been saved successfully.']);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation errors
        return redirect()->back()->withErrors($e->validator)->withInput();
    } catch (\Exception $e) {
        // Handle general exceptions
        return redirect()->back()->with(['class' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
    
}
    


public function all(Request $request, $id)
{
    $company = Company::find($id);
    $currentDate = Carbon::now();

    if ($request->wantsJson()) {
        // Determine start and end dates based on user input or current month/year
        $selectedMonth = $request->input('selected_month');
        $selectedYear = $request->input('selected_year');

        if ($selectedMonth && $selectedYear) {
            $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfDay()->format('Y-m-d');
            $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->endOfDay()->format('Y-m-d');
        } else {
            $startDate = $currentDate->startOfMonth()->startOfDay()->format('Y-m-d');
            $endDate = $currentDate->endOfMonth()->endOfDay()->format('Y-m-d');
        }

        // Search term
        $search = $request->input('search.value', '');

        // Query to get attendance records along with total overtime
        $query = Attendance::where('attendances.company_id', $id)
            ->join('admins', 'admins.id', '=', 'attendances.employee_id')
            ->leftJoin('admin_details', 'admin_details.admin_id', '=', 'admins.id')
            ->select([
                'attendances.id as attendance_id',
                'admins.id as employee_id',
                'admins.name as employee_name',
                'attendances.status',
                'attendances.hours',
                'attendances.overtime', // Include overtime in select
                'attendances.attendance_date',
                'admin_details.employee_code as code'
            ]);

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('admins.name', 'like', "%{$search}%")
                  ->orWhere('admin_details.employee_code', 'like', "%{$search}%");
            });
        }

        // Apply date filters
        $query->whereBetween('attendances.attendance_date', [$startDate, $endDate]);

        // Fetch the records
        $records = $query->get();

        // Initialize transformed data array
        $transformedData = [];

        // Fill in the transformed data
        foreach ($records as $record) {
            // Calculate the day index (0-30) for the attendance array
            $dayIndex = Carbon::parse($record->attendance_date)->day - 1;

            // Create employee entry if it doesn't exist
            if (!isset($transformedData[$record->code])) {
                $transformedData[$record->code] = [
                    'employee_name' => $record->employee_name,
                    'employee_code' => $record->code,
                    'attendance' => array_fill(0, 31, ['status' => '-', 'overtime' => 0]), // Fill array with default status and overtime
                    'total_overtime' => 0 // Initialize total overtime
                ];
            }

            // Set attendance status and overtime in the array
            if ($record->status === 'present') {
                $transformedData[$record->code]['attendance'][$dayIndex]['status'] = 'P';
            } elseif ($record->status === 'absent') {
                $transformedData[$record->code]['attendance'][$dayIndex]['status'] = 'A';
            } elseif ($record->hours) {
                $transformedData[$record->code]['attendance'][$dayIndex]['status'] = 'H'; // Assuming 'H' for hours
            }

            // Set daily overtime
            $transformedData[$record->code]['attendance'][$dayIndex]['overtime'] = $record->overtime ?? 0;

            // Accumulate total overtime
            $transformedData[$record->code]['total_overtime'] += $record->overtime ?? 0;
        }

        // Prepare final data structure
        $finalData = array_values($transformedData); // Reset the keys for the final data array
        // dd($finalData); // Debugging output to check the transformed data
        // JSON response
        return response()->json([
            'data' => $finalData,  // Return unique employee records
            'recordsTotal' => count($finalData),  // Count of unique employee records
            'recordsFiltered' => count($finalData),  // Count of filtered records
        ]);
    }

    return view('admin.attendance.all', compact('company'));
}







    
}


