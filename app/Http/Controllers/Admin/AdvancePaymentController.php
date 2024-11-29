<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdvancePayment;
use App\Models\Company;
use App\Models\Employee;
use Carbon\Carbon;
use App\Http\Resources\Admin\Company\CompanyCollection;
use App\Http\Resources\Admin\AdvancePayment\AdvancePaymentCollection;
use App\Models\Attendance;
use App\Models\EmployeeSalary;
use DB;


class AdvancePaymentController extends Controller
{
    public function create()
    {
        $employees = Employee::all();
        $companies=Company::where('company_type','company')->get();
        // dd($companies);
        return view('admin.advancepayment.create', compact('employees','companies'));
    }
    public function getEmployees(Request $request)
    {
        $companyId = $request->company_id;
    
        $employees = Employee::where('company_id', $companyId)->pluck('name', 'id');
    
        return response()->json($employees);
    }
    

    // Store advance payment (both one-time and EMI)
    public function store(Request $request)
{
    // Extract the payment data from the request
    $payments = $request->input('payment');
    
    // Filter out employees without any advance amount
    $filteredPayments = array_filter($payments, function ($paymentData) {
        return !empty($paymentData['amount']) && !empty($paymentData['payment_type']);
    });

    // Validation rules
    $validationRules = [
        'company_id' => 'required|exists:admins,id',
    ];

    foreach ($filteredPayments as $employeeId => $paymentData) {
        $validationRules["payment.$employeeId.amount"] = 'required|numeric|min:1';
        $validationRules["payment.$employeeId.payment_type"] = 'required|in:monthly,emi';

        if ($paymentData['payment_type'] === 'emi') {
            $validationRules["payment.$employeeId.interest_rate"] = 'required|numeric|min:0|max:100';
            $validationRules["payment.$employeeId.total_payment_amount"] = 'required|numeric|min:0';
            $validationRules["payment.$employeeId.emi_amount"] = 'required|numeric|min:1';
            $validationRules["payment.$employeeId.total_emi_count"] = 'required|integer|min:1';
        }
        $validationRules["payment.$employeeId.remarks"] = 'nullable|string|max:255';
    }

    // Perform validation
    $validated = $request->validate($validationRules, [
        'payment.*.amount.required' => 'The amount field is required for at least one employee.',
        'payment.*.payment_type.required' => 'The payment type field is required for at least one employee.',
    ]);

    // Initialize array for successful entries
    $successPayments = [];

    // Loop through each filtered employee's payment details
    foreach ($filteredPayments as $employeeId => $paymentData) {
        try {
            $paymentType = $paymentData['payment_type'];
            $totalEmiCount = $paymentType === 'emi' ? $paymentData['total_emi_count'] : 1;

            // Get current date for EMI scheduling
            $emiDate = Carbon::now('Asia/Kolkata');

            // Loop for each EMI installment and create entries
            for ($emiIndex = 0; $emiIndex < $totalEmiCount; $emiIndex++) {
                // Increment the month for each EMI
                $emiMonth = $emiDate->copy()->addMonths($emiIndex)->month;
                $emiYear = $emiDate->copy()->addMonths($emiIndex)->year;

                // Create individual EMI payment entries
                AdvancePayment::create([
                    'company_id' => $request->company_id,
                    'employee_id' => $employeeId,
                    'amount' => $paymentData['amount'],
                    'interest' => $paymentData['interest_rate'] ?? null,
                    'total_payable_amount' => $paymentData['total_payment_amount'] ?? null,
                    'emi_amount' => $paymentType === 'emi' ? $paymentData['emi_amount'] : null,
                    'total_emi_count' => $totalEmiCount,
                    'pending_emi_count' => $totalEmiCount - $emiIndex, // Update as pending EMIs decrease
                    'payment_type' => $paymentType,
                    'status' => 'active',
                    'date_taken' => Carbon::now('Asia/Kolkata'),
                    'description' => $paymentData['remarks'],
                    'emi_month' => $emiMonth,
                    'emi_year' => $emiYear,
                ]);
            }

            $successPayments[] = $employeeId;
            // dd($successPayments);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error adding advance payment for employee ID ' . $employeeId . ': ' . $e->getMessage());
        }
    }

    // Redirect with success message
    return redirect()->back()->with(['class' => 'success', 'message' => 'Payments added successfully for employees: ' . implode(', ', $successPayments)]);
}
    
    

    
    

    // List all advance payments for employees
    public function index(Request $request)
    {
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
        return view('admin.advancepayment.list');
    }

    // View details of a specific advance payment
    public function show($id)
    {
        $company = Company::findOrFail($id);
        $employees =Employee::where('company_id',$id)->with('employeedetail')->get();
        return view('admin.advancepayment.view',compact('company','employees'));
    }
    public function all(Request $request, $id)
    {
        $company = Company::find($id);
        
        if ($request->wantsJson()) {
            $search = $request->search['value'];
            $startDate = $request->start_date; 
            $endDate = $request->end_date; 
    
            $datas = AdvancePayment::where('advance_payments.company_id', $id)
    ->join('admins', 'admins.id', '=', 'advance_payments.employee_id')
    ->leftJoin('admin_details', 'admin_details.admin_id', '=', 'admins.id')
    ->select([
        'admins.id as employee_id',
        'admins.name as employee_name',
        'admin_details.employee_code as employee_code',
        // Get the latest total_amount, total_interest, and total_emi_amount without summing
        DB::raw('MAX(advance_payments.amount) as total_amount'),
        DB::raw('MAX(advance_payments.total_payable_amount) as total_payable_amount'),
        DB::raw('MAX(advance_payments.interest) as total_interest'),
        DB::raw('MAX(advance_payments.payment_type) as payment_type'),
        DB::raw('MAX(advance_payments.emi_amount) as total_emi_amount'),
        DB::raw('COUNT(advance_payments.id) as total_emi_count'),
        DB::raw('SUM(CASE WHEN advance_payments.status = "active" THEN 1 ELSE 0 END) as pending_emi_count'),
        DB::raw('MAX(advance_payments.date_taken) as latest_date_taken')
    ])
    ->groupBy('admins.id', 'admins.name', 'admin_details.employee_code');

    
            // Add search functionality
            if ($search) {
                $datas = $datas->where(function($query) use ($search) {
                    $query->where('admins.name', 'like', "%{$search}%")
                          ->orWhere('admin_details.employee_code', 'like', "%{$search}%");
                });
            }
    
            // Date filters
            if ($startDate) {
                $datas = $datas->where('advance_payments.date_taken', '>=', $startDate);
            }
            if ($endDate) {
                $datas = $datas->where('advance_payments.date_taken', '<=', $endDate);
            }
    
            // Sort results
            $datas = $datas->orderBy('advance_payments.created_at', 'desc')
                           ->orderBy('admins.name', 'asc');
    
            // Total records count for DataTable
            $request->merge(['recordsTotal' => $datas->count(), 'length' => $request->length]);
            
            // Paginate results for DataTable
            $datas = $datas->limit($request->length)->offset($request->start)->get();
            // dd($datas);
            // Return data in JSON format
            return response()->json(new AdvancePaymentCollection($datas));
        }
    
        // If not an AJAX request, return the view
        return view('admin.advancepayment.all', compact('company'));
    }
    

public function getWorkingDays(Request $request)
{
    $companyId = 15;
    $startDate = '2024-10-01';
    $endDate = '2024-10-31';
    $employees = Employee::where('company_type', 'employee')->where('company_id', $companyId)->get();
    $attendanceData = [];
    
    foreach ($employees as $employee) {
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();
        $advancePayments = AdvancePayment::where('company_id', $companyId)
            ->where('employee_id', $employee->id)
            ->where('status', 'active')
            ->get();
        $totalWorkingDays = 0;
        $totalLeaves = 0;
        $totalHours = 0;
        $totalOvertime = 0;
        foreach ($attendances as $attendance) {
            if ($attendance->status == 'present') {
                $totalWorkingDays++;
                if ($attendance->overtime) {
                    $totalOvertime += $attendance->overtime;
                }
            } elseif ($attendance->status == 'absent') {
                $totalLeaves++;
            } elseif ($attendance->status == 'hours') {
                $totalHours += $attendance->hours;
                if ($attendance->overtime) {
                    $totalOvertime += $attendance->overtime;
                }
            }
        }
        $advanceDetails = [];
        foreach ($advancePayments as $advance) {
            $advanceDetails[] = [
                'amount_taken' => $advance->amount,
                'interest' => $advance->interest,
                'total_payable_amount' => $advance->total_payable_amount,
                'emi_amount' => $advance->emi_amount,
                'total_emi_count' => $advance->total_emi_count,
                'pending_emi_count' => $advance->pending_emi_count,
                'status' => $advance->status,
            ];
        }
        $fillableFields = (new EmployeeSalary())->getFillable();
        $salaryDetails = [];
        $salary=EmployeeSalary::where('admin_id',$employee->id) ->select($fillableFields)->first();
        $salaryDetails[]=[
            'basic_salary' => $salary->basic_salary,
                'pf_basic' => $salary->pf_basic,
                'hra' => $salary->hra,
                'allowance' => $salary->allowance,
                'lwf' => $salary->lwf,
                'deduction' => $salary->deduction,
                'conveyance' => $salary->conveyance,
                'actual_salary' => $salary->actual_salary,
                'ovr_time_rate' => $salary->ovr_time_rate,

        ];
            $workdays = round($totalWorkingDays + ($totalHours + $totalOvertime) / 8, 2);
            $otsalary=round(($salary->actual_salary/30)/$salary->ovr_time_rate)*$totalOvertime;
        $attendanceData[] = [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'total_working_days' => $totalWorkingDays,
            'total_leaves' => $totalLeaves,
            'total_hours' => $totalHours,
            'total_overtime' => $totalOvertime,
            'workdays' => $workdays,
            'advance_payment_details' => $advanceDetails, 
            'salaryDtails'=>$salaryDetails,
            'salary_of_this_month'=>round((($salary->actual_salary/30)* $totalWorkingDays)+$otsalary+(($salary->actual_salary/30)/8)*$totalHours)
        ];
    }
    dd($attendanceData);
}



}
