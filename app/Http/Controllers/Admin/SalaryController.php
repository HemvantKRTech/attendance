<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendanceData;
use App\Exports\SalaryExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\AdvancePayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use ZipArchive;


class SalaryController extends Controller
{
    public function index(){
        $companies = Company::
        where('company_type', 'company')
        ->pluck('name', 'id');
    
        return view('admin.salary.create',compact('companies'));
    }
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'company_id' => 'required|exists:admins,id',
            'year' => 'required|',
            'month' => 'required|',
        ]);
    
        $month = Carbon::parse($validated['month'])->month;
        $companyId = $validated['company_id'];
        $startDate = Carbon::createFromDate($validated['year'], $month, 1)->startOfMonth()->format('Y/m/d');
        $endDate = Carbon::createFromDate($validated['year'], $month, 1)->endOfMonth()->format('Y/m/d');
        $daysInMonth = Carbon::createFromDate($validated['year'], $month, 1)->daysInMonth;
    
        // Get employees
        $employees = Employee::where('company_type', 'employee')->where('company_id', $companyId)->get();
        $attendanceData = [];
        $exportdata = []; 
        foreach ($employees as $employee) {
            // dd($employee->id);
            // Get attendance records for the month
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->get();
            // Get advance payments
            $advancePayments = AdvancePayment::where('company_id', $companyId)
                ->where('employee_id', $employee->id)
                ->where('emi_month',$month)->where('emi_year', $validated['year'])
                ->get();
                // dd( $advancePayments);
    
            // Initialize counters
            $totalWorkingDays = 0;
            $totalLeaves = 0;
            $totalHours = 0;
            $totalOvertime = 0;
    
            foreach ($attendances as $attendance) {
                if ($attendance->status == 'present') {
                    $totalWorkingDays++;
                    $totalOvertime += $attendance->overtime ?? 0; // Use null coalescing to avoid errors
                } elseif ($attendance->status == 'absent') {
                    $totalLeaves++;
                } elseif ($attendance->status == 'hours') {
                    $totalHours += $attendance->hours;
                    $totalOvertime += $attendance->overtime ?? 0; // Use null coalescing to avoid errors
                }
            }
    
            // Prepare advance payment details
            $advanceDetails = [];
          $totalAdvance = 0;
        //   $advanceAmount = 0;
            foreach ($advancePayments as $advance) {
                $amountTaken = $advance->amount ?? 0;
                $emiAmount = $advance->emi_amount ?? 0; 
                // dd($emiAmount);
                if ($advance->payment_type === 'emi') {
                    $advanceAmount = $emiAmount;
                    // dd($advanceAmount);
                } elseif ($advance->payment_type === 'monthly') {
                    $advanceAmount = $amountTaken;
                } else {
                    $advanceAmount = 0;
                }
                // dd($advanceAmount);
                // Store advance details for the current advance payment
                $advanceDetails[] = [
                    'amount_taken' => $amountTaken,
                    'interest' => $advance->interest ?? 0,
                    'total_payable_amount' => $advance->total_payable_amount ?? 0,
                    'emi_amount' => $emiAmount,
                    'total_emi_count' => $advance->total_emi_count ?? 0,
                    'pending_emi_count' => $advance->pending_emi_count ?? 0,
                    'status' => $advance->status ?? 'unknown',
                    'advance' => $advanceAmount,
                ];
            
                // Calculate total advance
                $totalAdvance += $advanceAmount; // Accumulate total advance
            }
            // dd($totalAdvance);
            // $advance=$advanceDetails['amount_taken']+$advanceDetails['emi_amount'];
            // Fetch the salary details
            $salary = EmployeeSalary::where('admin_id', $employee->id)->first();
            if (!$salary) {
                continue; // Skip if no salary found
            }
            $company = Company::find($companyId);
            $pldays = $company->pl_days;
            $pl_add = $company->pl_add ?? 0; // Ensure pl_add has a default in case it's null
            
            // Adjust workdays if they match the paid leave threshold
            if ($totalWorkingDays == $pldays) {
                $totalWorkingDays += $pl_add;
            }
            // Calculate workdays and overtime salary
            $workdays = round($totalWorkingDays + ($totalHours + $totalOvertime) / 8, 2);
            $otsalary = round(($salary->actual_salary / 30) / $salary->ovr_time_rate) * $totalOvertime;
           
            // Prepare attendance data for export
            $attendanceData[] = [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'total_working_days' => $totalWorkingDays,
                'total_leaves' => $totalLeaves,
                'total_hours' => $totalHours,
                'overtime_salary'=> $otsalary,
                'total_overtime' => $totalOvertime,
                'workdays' => $workdays,
                'advance_payment_details' => $advanceDetails,
                'salary_details' => [
                    'basic_salary' => $salary->basic_salary,
                    'pf_basic' => $salary->pf_basic,
                    'hra' => $salary->hra,
                    'allowance' => $salary->allowance,
                    'lwf' => $salary->lwf,
                    'deduction' => $salary->deduction,
                    'conveyance' => $salary->conveyance,
                    'actual_salary' => $salary->actual_salary,
                    'ovr_time_rate' => $salary->ovr_time_rate,
                ],
                'advance' => $totalAdvance,
                'actual_salary' =>  $salary->actual_salary,
                'salary_of_this_month' => round((($salary->actual_salary / $daysInMonth) * $workdays) - $totalAdvance),
            ];
    // dd($attendanceData);
            // Salary Calculation Logic
            $basic = $salary->basic_salary;
            $hra = $salary->hra;
            $pfBasic = $salary->pf_basic;
            $conveyance = $salary->conveyance;
            $otherAllowance = $salary->deduction;
    
            // Simulated value for totalAmount
            // $totalAmount = $attendanceData[count($attendanceData) - 1]['salary_of_this_month']; // Get the last entry's salary
// Check if $attendanceData is not empty
if (!empty($attendanceData)) {
    // Safely get the last element's 'salary_of_this_month'
    $lastIndex = count($attendanceData) - 1;
    $totalAmount = $attendanceData[$lastIndex]['salary_of_this_month'] ?? 0; // Use null coalescing to provide a default value
} else {
    // Handle the case where there are no attendance data
    $totalAmount = 0; // Or handle as needed
}

            // dd($totalAmount);
            // Initial variables
            $workingDays = $daysInMonth; // Adjust as necessary
            $totaldays = $daysInMonth; // Total days in the month
    
            // Salary calculations
            if ($totalAmount == 0) {
                $workingDays = 0; // Set working days to zero
    
                // Create a temporary salary record with zeros
                $tempMonthlySalary = [
                    // 'company_id' => $companyId,
                    'employee_name' => $employee->name,
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                    'working_days' => $workingDays,
                    'basic' => intval(round($basic)),
                    'pf_basic' => intval(round($pfBasic)),
                    'hra' => 0,
                    'conveyance' => intval(round($conveyance)),
                 'other_allowance' => intval(round((float)$otherAllowance)),

                    'basic_amount' => 0,
                    'pf_basic_amount' => 0,
                    'hra_amount' => 0,
                    'conveyance_amount' => 0,
                    'other_allowance_amount' => 0,
                    'rate_of_pay' => 0,
                    'epf_employee' => 0,
                    'epf_employer' => 0,
                    'eps_employer' => 0,
                    'esi_employee' => 0,
                    'esi_employer' => 0,
                    'psdt_amount' => 0,
                    'tds_amount' => 0,
                    'lwf_employer' => 0,
                    'lwf_employee' => 0,
                    'other_if_any' => 0,
                    'total_deductions' => 0,
                    'net_payable' => 0,
                    'cash' => 0,
                    'advance' => $totalAdvance,
                ];
                // dd($tempMonthlySalary);
                // Add to attendance data
                $attendanceData[] = $tempMonthlySalary;
                continue; // Move to the next employee
            }
    
            // Create a temporary salary record for non-zero total amount
            $tempMonthlySalary = [
                // 'company_id' => $companyId,
                'employee_name' => $employee->name,
                'month' => $validated['month'],
                'year' => $validated['year'],
            ];
    
            do {
                // Calculate totalAmount again before each iteration if needed
                $totalAmount = $attendanceData[count($attendanceData) - 1]['salary_of_this_month']; // Get the last entry's salary
                // dd($totalAmount);
                // Set working days
                $tempMonthlySalary['working_days'] = $workingDays;
                $tempMonthlySalary['basic'] = $basic;
                $tempMonthlySalary['pf_basic'] = $pfBasic;
                $tempMonthlySalary['hra'] = $hra;
                $tempMonthlySalary['conveyance'] = $conveyance;
                $tempMonthlySalary['other_allowance'] = $otherAllowance;
                $tempMonthlySalary['actual_salary'] = $salary->actual_salary;
            
                // Calculate amounts based on working days
                $tempMonthlySalary['basic_amount'] = ((float)$basic / $totaldays) * $workingDays;
                $tempMonthlySalary['pf_basic_amount'] = ((float)$pfBasic / $totaldays) * $workingDays;
                $tempMonthlySalary['hra_amount'] = ((float)$hra / $totaldays) * $workingDays;
                $tempMonthlySalary['conveyance_amount'] = ((float)$conveyance / $totaldays) * $workingDays;
                $tempMonthlySalary['other_allowance_amount'] = ((float)$otherAllowance / $totaldays) * $workingDays;
            
                // Calculate total salary
                $salaryAndWages = $tempMonthlySalary['basic_amount'] +
                                  $tempMonthlySalary['hra_amount'] +
                                  $tempMonthlySalary['conveyance_amount'] +
                                  $tempMonthlySalary['other_allowance_amount'];
            
                // Deductions based on working days
                $tempMonthlySalary['rate_of_pay'] = $salaryAndWages;
                if($tempMonthlySalary['other_allowance'] == 'Cash'){
                    $tempMonthlySalary['epf_employee'] =0;
                    $tempMonthlySalary['epf_employer'] = 0;
                    $tempMonthlySalary['eps_employer'] = 0;
                    $tempMonthlySalary['esi_employee'] =0;
                    $tempMonthlySalary['esi_employer'] =0 ;
                    $tempMonthlySalary['psdt_amount'] =0;
                    $tempMonthlySalary['tds_amount'] = 0;
                    $tempMonthlySalary['lwf_employer'] = 0 ;
                    $tempMonthlySalary['lwf_employee'] = 0 ;
                    $tempMonthlySalary['other_if_any'] = 0;
                }else{
                    $tempMonthlySalary['epf_employee'] = ($pfBasic * 0.12) * ($workingDays / $totaldays);
                    $tempMonthlySalary['epf_employer'] = ($pfBasic * 0.0367) * ($workingDays / $totaldays);
                    $tempMonthlySalary['eps_employer'] = ($pfBasic * 0.0833) * ($workingDays / $totaldays);
                    $tempMonthlySalary['esi_employee'] = $salaryAndWages > 21000 ? 0 : ($salaryAndWages * 0.0075) ;
                    $tempMonthlySalary['esi_employer'] = $salaryAndWages > 21000 ? 0 : ($salaryAndWages * 0.0325) ;
                    $tempMonthlySalary['psdt_amount'] = $salaryAndWages > 21000 ? 200 : 0;
                    $tempMonthlySalary['tds_amount'] = 0;
                    $tempMonthlySalary['lwf_employer'] = 20 ;
                    $tempMonthlySalary['lwf_employee'] = 5 ;
                    $tempMonthlySalary['other_if_any'] = 0;
                }
                
            
                // Total deductions
                $tempMonthlySalary['total_deductions'] = 
                    $tempMonthlySalary['epf_employee'] + 
                    $tempMonthlySalary['esi_employee'] + 
                    $tempMonthlySalary['psdt_amount'] + 
                    $tempMonthlySalary['tds_amount'] + 
                    $tempMonthlySalary['other_if_any'] + 
                    $tempMonthlySalary['lwf_employee'];
            // dd($tempMonthlySalary['other_allowance']);
                // Calculate net payable salary
                if ($tempMonthlySalary['other_allowance'] == 'Transfer'  ) {
                    $tempMonthlySalary['net_payable'] = $salaryAndWages;
                }elseif($tempMonthlySalary['other_allowance'] == 'Cash'){
                    $tempMonthlySalary['net_payable'] = null;

                } else {
                    $tempMonthlySalary['net_payable'] = $salaryAndWages - $tempMonthlySalary['total_deductions'];
                }
                
            
                // Reduce working days if total amount is less than required deductions
                $totalRequiredDeductions = 
                    $tempMonthlySalary['epf_employee'] + 
                    $tempMonthlySalary['esi_employee'] + 
                    $tempMonthlySalary['lwf_employee'] + 
                    $tempMonthlySalary['net_payable'];
                $tempMonthlySalary['advance'] = $totalAdvance;
                $tempMonthlySalary['amount'] = $totalAmount;
                // dd( $totalAmount);
                // Recalculate total amount here if necessary
                // $totalAmount = $attendanceData[count($attendanceData) - 1]['salary_of_this_month']; // Refresh the total amount
                if ($tempMonthlySalary['other_allowance'] == 'Transfer' ) {
                    $tempMonthlySalary['cash'] = $totalAmount - $totalAdvance  - $tempMonthlySalary['net_payable'];
                } elseif($tempMonthlySalary['other_allowance'] == 'Cash') {
                    $tempMonthlySalary['cash'] = $tempMonthlySalary['rate_of_pay']- $totalAdvance;
                }else{
                    $tempMonthlySalary['cash'] = $totalAmount - $tempMonthlySalary['net_payable'] - $totalAdvance - $tempMonthlySalary['total_deductions'];

                }
                // $tempMonthlySalary['cash'] = $totalAmount - $tempMonthlySalary['net_payable'] - $totalAdvance - $tempMonthlySalary['total_deductions']; // Updated cash value
                $attendanceData['esi']=$tempMonthlySalary['esi_employee'];
                $workingDays--;
            } while ($totalAmount < $totalRequiredDeductions && $workingDays > 0);
            
    
            // Add to attendance data
            $exportdata[] = $tempMonthlySalary;
            
        }
        // Export the salary data to Excel
        try {
            // dd($attendanceData);
            AdvancePayment::where('company_id', $companyId)
        ->whereIn('employee_id', $employees->pluck('id')->toArray())
        ->where('status', 'active')
        ->where('emi_month', $month)
        ->where('emi_year', $validated['year'])
        ->update(['status' => 'completed']);
        // return Excel::download(new AttendanceData($attendanceData), 'aatendance.xlsx');
        
        //     return Excel::download(new SalaryExport($exportdata), 'salaryautomation.xlsx');
        return $this->exportAttendanceAndSalary($attendanceData, $exportdata);
        } catch (\Exception $e) {
            // dd($e);
            \Log::error('Error exporting salary data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an error exporting the file: ' . $e->getMessage())->withInput();
        }
    }
    public function exportAttendanceAndSalary($data, $exportdata)
{
    // Step 1: Define paths for the individual Excel files within the storage/app folder
    $attendanceFilePath = storage_path('app/attendance.xlsx');
    $salaryFilePath = storage_path('app/salaryautomation.xlsx');

    // Step 2: Generate and store the attendance file
    Excel::store(new AttendanceData($data), 'attendance.xlsx');

    // Step 3: Generate and store the salary file
    Excel::store(new SalaryExport($exportdata), 'salaryautomation.xlsx');

    // Step 4: Create a ZIP file to bundle both Excel files in storage/app folder
    $zipFilePath = storage_path('app/attendance_and_salary_files.zip');
    $zip = new ZipArchive();

    // Step 5: Open the ZIP file and add the attendance and salary files
    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $zip->addFile($attendanceFilePath, 'attendance.xlsx');
        $zip->addFile($salaryFilePath, 'salaryautomation.xlsx');
        $zip->close();
    } else {
        return redirect()->back()->with('error', 'Unable to create ZIP file');
    }

    // Step 6: Return the ZIP file as a download response
    return response()->download($zipFilePath)->deleteFileAfterSend(true);
}
    
    
    
}
