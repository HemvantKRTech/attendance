<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Role;
use App\Models\EmployeeDetails;
use App\Http\Resources\Admin\Employee\EmployeeCollection;
use Illuminate\Support\Facades\DB;
use App\Models\State;
use App\Models\City;
use App\Models\District;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmployeeDetailsImport;
use App\Models\Department;
use App\Models\Tempemployeedetails;
use App\Models\Wage;
use Exception;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // Make sure Carbon is imported



class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
     
             // Example input
            
        if ($request->wantsJson()) {
       
            $datas = Employee::with(['company' => function ($query) {
                $query->select('id', 'name'); 
            }])
            ->join('roles', 'roles.id', '=', 'admins.role_id')
            ->orderBy('admins.created_at', 'desc')
            ->whereNotNull('admins.company_id') 
            ->select([
                'admins.id as id',
                'roles.name as role',
                'admins.name as name',
                'admins.mobile as mobile',
                'admins.email as email',
                'admins.status',
                'admins.company_id',
                
            ]);
        
        $request->merge(['recordsTotal' => $datas->count(), 'length' => $request->length]);
        $datas = $datas->limit($request->length)->offset($request->start)->get();
        // dd($datas);

        return response()->json(new EmployeeCollection($datas));
        }
        return view('admin.employee.list');
   
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies=Company::where('role_id',3)->get();
       $roles = Role::whereNotIn('id', [1, 2, 3])->get();
       $state=State::all();
       $skills=Wage::where('is_active',1)->get();
    //    dd($skill);


        return view('admin.employee.create',compact('companies','roles','state','skills'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            
            'company_id' => 'required', // Only required, no existence check
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255', // Only required, no uniqueness check
            'fathername' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'aadhar_no' => 'required|string|max:12',
            'mobile' => 'required|string|max:10|regex:/^\d{10}$/',
            'ac_no' => 'required|string|max:20',
            'bank_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:11',
            'esic_no' => 'nullable|string|max:20',
            'pf_no' => 'nullable|string|max:20',
            'date_of_birth' => 'required',
            'role_id' => 'required|integer', // Ensure role_id is provided and is an integer
            'state' => 'required', // Only required
            'distt' => 'required', // Only required
            'city' => 'required', // Only required
            'location' => 'nullable|string|max:255',
            'employee_code' => 'required|string|max:50', // Add validation for employee_code
            'designation' => 'required|string|max:100', // Add validation for designation
            'department_id' => 'required|string|max:100', // Add validation for department
            'employment_type' => 'required|in:Permanent,Daily Wages,Contract',
            'date_of_joining'=>'required',
            'date_of_releiving'=>'nullable',
            'skill'=>'required',
            // 'ovr_time_rate'=>'required',
            // 'salary'=>'required|number',
        ]);
    // dd($validatedData);
        DB::enableQueryLog();
        DB::beginTransaction();
    
        try {
            // Step 1: Create the employee in the Employee model
            $employee = Employee::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'gender' => $request->input('gender'),
                'date_of_birth' => $request->input('date_of_birth'),
                'company_id' => $request->input('company_id'),
                'skill_type' => $request->input('skill'),
                // 'ovr_time_rate'=>$request->input('ovr_time_rate'),
                'role_id' => $request->input('role_id'), // Add role_id here
            ]);
    
            // Step 2: Create the EmployeeDetails record
            EmployeeDetails::create([
                'date_of_releiving'=>$request->input('date_of_releiving'),
                'date_of_joining'=>$request->input('date_of_joining'),

                'fathername' => $request->input('fathername'),
                'admin_id' => $employee->id, // Link to the created employee
                'gender' => $request->input('gender'),
                'aadhar_no' => $request->input('aadhar_no'),
                'mobile' => $request->input('mobile'),
                'ac_no' => $request->input('ac_no'),
                'bank_name' => $request->input('bank_name'),
                'ifs_code' => $request->input('ifsc_code'),
                'esic_no' => $request->input('esic_no'),
                'epf_no' => $request->input('pf_no'),
                'state_id' => $request->input('state'),
                'distt_id' => $request->input('distt'),
                'city_id' => $request->input('city'),
                'location' => $request->input('location'),
                'nationality' => $request->input('nationality'),
                'employee_code' => $request->input('employee_code'), // Add employee_code here
                'designation' => $request->input('designation'), // Add designation here
                'department' => $request->input('department_id'), // Add department here
                'employment_type' => $request->input('employment_type'), 
            ]);
    
            // Commit the transaction
            DB::commit();
    
            // Get the executed queries
            $queries = DB::getQueryLog();
            // dd($queries); // Print the executed queries
    
            return redirect()->back()->with(['class' => 'success', 'message' => 'Employee created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error message
            Log::error('Error creating employee: ' . $e->getMessage());
            // Output the error for debugging
            dd($e); // Show the exception for debugging
            return redirect()->back()->with(['class' => 'error', 'message' => 'Failed to create Employee. Please try again later.']);
        }
    }
    


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::with('company')->findOrFail($id);
        $employeeDetails = EmployeeDetails::where('admin_id', $employee->id)->first();
        $department=Department::find($employeeDetails->department);
        // dd($department);
        return view('admin.employee.view',compact('employee', 'employeeDetails','department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = Employee::with('company','employeedetail.city','employeedetail.state','employeedetail.district')->find($id);
        // dd($employee);
        $companies=Company::where('role_id',3)->get();
       $roles = Role::whereNotIn('id', [1, 2, 3])->get();
       $states = State::pluck('state_title', 'id')->toArray();
       $city = City::pluck('name', 'id')->toArray(); 
       $district = District::pluck('district_title', 'id')->toArray(); 
       $employeedepartment=Department::find($employee->employeedetail->department);
    //    $employeedepartment=$employeedepartment->id;
    //    dd($employeedepartment);
        $departments=Department::all();
        $skills=Wage::where('is_active',1)->get();
        return view('admin.employee.edit',compact('skills','employee','companies','roles','states','city','district','employeedepartment','departments'));
    }
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'company_id' => 'required', // Only required, no existence check
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255', // Only required, no uniqueness check
            'fathername' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'aadhar_no' => 'required|string|max:12',
            'mobile' => 'required|string|max:10|regex:/^\d{10}$/',
            'ac_no' => 'required|string|max:20',
            'bank_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:11',
            'esic_no' => 'nullable|string|max:20',
            'pf_no' => 'nullable|string|max:20',
            'date_of_birth' => 'required',
            'role_id' => 'required|integer', // Ensure role_id is provided and is an integer
            'state' => 'required', // Only required
            'distt' => 'required', // Only required
            'city' => 'required', // Only required
            'location' => 'nullable|string|max:255',
            'employee_code' => 'required|string|max:50', // Add validation for employee_code
            'designation' => 'required|string|max:100', // Add validation for designation
            'department_id' => 'required|string|max:100', // Add validation for department
            'employment_type' => 'required|in:Permanent,Daily Wages,Contract',
            'date_of_joining'=>'required',
            'date_of_releiving'=>'nullable',
        ]);
        // dd($validatedData);
    
        // DB::beginTransaction();
    
        try {
            $employee = Employee::findOrFail($id);
            $employee->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'gender' => $request->input('gender'),
                'date_of_birth' => $request->input('date_of_birth'),
                'company_id' => $request->input('company_id'),
                'role_id' => $request->input('role_id'),
            ]);
            $employee->employeedetail()->updateOrCreate(
                ['admin_id' => $employee->id], // Ensure you're using the correct key to find or create
                [
                    'date_of_releiving' => $request->input('date_of_releiving'),
                    'date_of_joining' => $request->input('date_of_joining'),
                    'fathername' => $request->input('fathername'),
                    'gender' => $request->input('gender'),
                    'aadhar_no' => $request->input('aadhar_no'),
                    'mobile' => $request->input('mobile'),
                    'ac_no' => $request->input('ac_no'),
                    'bank_name' => $request->input('bank_name'),
                    'ifs_code' => $request->input('ifsc_code'),
                    'esic_no' => $request->input('esic_no'),
                    'epf_no' => $request->input('pf_no'),
                    'state_id' => $request->input('state'),
                    'distt_id' => $request->input('distt'),
                    'city_id' => $request->input('city'),
                    'location' => $request->input('location'),
                    'nationality' => $request->input('nationality'),
                    'employee_code' => $request->input('employee_code'),
                    'designation' => $request->input('designation'),
                    'department' => $request->input('department_id'),
                    'employment_type' => $request->input('employment_type'),
                ]
            );
            
            DB::commit();
            return redirect()->back()->with(['class' => 'success', 'message' => 'Employee updated successfully.']);
    
        } catch (Exception $e) {
            dd($e);
            DB::rollBack();
            Log::error('Error updating employee: ' . $e->getMessage());
            return redirect()->back()->with(['class' => 'danger', 'message' => 'Failed to update employee. Please try again later.']);
        }
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Employee=Employee::find($id);
        $Employee->delete();
        return redirect()->json()->with(['class' => 'success', 'message' => 'Employee deleted successfully.']);
       
    }
   
    public function salary($id)
    {
        $salary = EmployeeSalary::where('admin_id', $id)->first();
        if (!$salary) {
            $salary = new EmployeeSalary();
        }
        return view('admin.employee.salary', compact('salary', 'id'));
    }
    
    public function storesalary(Request $request, $id){
        $request->validate([
            'basic_salary'      => 'required|numeric|min:0',
            'designation'      => 'required',
            'pf_basic'   => 'required|numeric|min:0',
            'hra'        => 'required|numeric|min:0',
            'allowance'  => 'required|numeric|min:0',
            // 'lwf'        => 'required|numeric|min:0',
            'deduction'  => 'required|in:PF,ESI,PF+ESI,Transfer,Cash',
            'conveyance' => 'required|numeric|min:0',
            'ovr_time_rate' => 'required|numeric|min:0',
            'actual_salary'=>'required|numeric'
        ]);
        $employee = Employee::findOrFail($id);
        EmployeeSalary::updateOrCreate(
            ['admin_id' => $employee->id], 
            [
                'basic_salary'      => $request->input('basic_salary'),
                'designation'      => $request->input('designation'),
                'pf_basic'   => $request->input('pf_basic'),
                'hra'        => $request->input('hra'),
                'allowance'  => $request->input('allowance'),
                // 'lwf'        => $request->input('lwf'),
                'deduction'  => $request->input('deduction'),
                'ovr_time_rate'  => $request->input('ovr_time_rate'),
                'conveyance' => $request->input('conveyance'),
                'actual_salary' => $request->input('actual_salary')

            ]
        );
        return redirect()->back()->with(['class' => 'success', 'message' => 'Salary details updated successfully.']);
    }
    public function import(){
        $companies = Company::where('entity_type', 'company')->get();
        return view('admin.employee.import', compact('companies'));
    }

    public function storeimport(Request $request){
        $request->validate([
        'company_id' => 'required|exists:admins,id',
        'company_excel' => 'required|file|mimes:xlsx,xls,csv',
    ]);

        if ($request->hasFile('company_excel')) {
            $file = $request->file('company_excel');

            try {
                TempEmployeeDetails::truncate();
                $import=new EmployeeDetailsImport($request->company_id);
                Excel::import($import, $file);
                if (count($import->existingAadhars)>0) {
                    session()->flash('warning', 'Some Aadhar numbers already exist:');
                    session()->flash('existing_aadhars', $import->existingAadhars);
                }
              
                return redirect()->route('admin.employee.uploaded_data.excell', ['employee' => $request->company_id])->with(['class' => 'success', 'message' => 'Company data imported successfully.','existadhaar'=>$import->existingAadhars]);
            } catch (\Exception $e) {
                dd($e);
                return redirect()->back()->with(['class' => 'danger', 'message' => 'Failed to import Employee data. Please try again later.']);
            }
        }

        return redirect()->back()->with(['class' => 'danger', 'message' => 'No file was uploaded.']);
  
    }
    public function showUploadedData( $employee)
    {
            $employees = TempEmployeeDetails::where('company_id', $employee)->get();
        $aadharCounts = $employees->groupBy('aadhar_no')->map(function ($group) {
            return $group->count();
        });
        $repeatedAadhars = $aadharCounts->filter(function ($count) {
            return $count > 1; 
        });
        $aadharNos = $employees->pluck('aadhar_no')->toArray();
        $existingAadhars = EmployeeDetails::whereIn('aadhar_no', $aadharNos)->pluck('aadhar_no')->toArray();
        return view('admin.employee.uploadview', compact('employees', 'repeatedAadhars', 'existingAadhars','employee'));
    }

   


    public function verify(Request $request)
    {
        // dd($request->all());
        $company_id = $request->input('company_id');
        $employeeData = TempEmployeeDetails::where('company_id', $company_id)->get();
    
        foreach ($employeeData as $employee) {
            try {
                // dd($employee);
                // Check for existing Aadhar number
                $existingEmployee = EmployeeDetails::whereHas('employee', function ($query) use ($company_id) {
                    $query->where('company_id', $company_id);
                })->where('aadhar_no', $employee['aadhar_no'])->first();
    
                // If Aadhar number exists, add to the warning array and skip to next
                if ($existingEmployee) {
                    $existingAadhars[] = $existingEmployee->aadhar_no; // Store Aadhar in array
                    continue; // Skip if employee already exists
                }
        
                // Check for duplicates in the current upload
                $isRepeated = Employee::where('company_id', $company_id)
                    ->where('email', $employee['email'])
                    ->count() > 1;
                if ($isRepeated) {
                    continue; // Skip repeated records
                }
        
                // Get state and district IDs
                $state_id = getStateId($employee['state']);
                $district_id = getDistrictId($employee['district'], $state_id);
                // You can call `getCityId()` here if city logic is implemented
                
                // Prepare date fields using Carbon
                $date_of_birth = $this->parseDate($employee['date_of_birth'], 'd/m/Y');  // Adjust format if needed
                $date_of_joining = $this->parseDate($employee['date_of_joining'], 'd/m/Y'); // Adjust format if needed
                $date_of_relieving = $employee['date_of_relieving'] !== 'N/A' 
                                    ? $this->parseDate($employee['date_of_relieving'], 'd/m/Y') // Adjust format if needed
                                    : null;
    
                // Create and save Employee instance
                $newEmployee = new Employee();
                $newEmployee->name = $employee['employee_name'];
                $newEmployee->email = $employee['email'];
                $newEmployee->mobile = $employee['mobile'];
                $newEmployee->gender = $employee['gender'];
                $newEmployee->date_of_birth = $date_of_birth;
                $newEmployee->company_id = $company_id;
                $newEmployee->skillset = $employee['skill_level'];
                $newEmployee->save();
                
                // Create and save EmployeeDetails instance
                $newEmployeeDetails = new EmployeeDetails();
                $newEmployeeDetails->admin_id = $newEmployee->id;
                $newEmployeeDetails->father_or_husband_name = $employee['father_or_husband_name'];
                $newEmployeeDetails->aadhar_no = $employee['aadhar_no'];
                $newEmployeeDetails->ac_no = $employee['bank_account_no'];
                $newEmployeeDetails->bank_name = $employee['bank_name'];
                $newEmployeeDetails->ifs_code = $employee['ifsc_code'];
                $newEmployeeDetails->esic_no = $employee['esic_no'];
                $newEmployeeDetails->epf_no = $employee['pf_no'];
                $newEmployeeDetails->date_of_joining = $date_of_joining;
                $newEmployeeDetails->date_of_relieving = $date_of_relieving;
                $newEmployeeDetails->location = $employee['location'];
                $newEmployeeDetails->nationality = $employee['nationality'];
                $newEmployeeDetails->state_id = $state_id;
                $newEmployeeDetails->basic = $employee['basic'];
                $newEmployeeDetails->designation =  $employee['designation'];
                $newEmployeeDetails->pf_basic = $employee['pf_basic'];
                $newEmployeeDetails->hra = $employee['hra'];
                $newEmployeeDetails->allowance = $employee['allowance'];
                $newEmployeeDetails->lwf = $employee['lwf'];
                $newEmployeeDetails->deduction = $employee['deduction'];
                $newEmployeeDetails->conveyance = $employee['conveyance'];

                


                // $newEmployeeDetails->city_id = getCityId($employee['city'], $district_id); // Assuming you have this function implemented
                
                // Save EmployeeDetails instance
                $newEmployeeDetails->save();
                
            } catch (\Exception $e) {
                // Log the error and skip to the next employee
                dd($e);
                Log::error('Error processing employee: ' . json_encode($employee) . ' Error: ' . $e->getMessage());
                continue;
            }
        }
    
        // Truncate TempEmployeeDetails after processing
        TempEmployeeDetails::truncate();
    
        return redirect()->route('admin.employee.index')->with('success', 'Employee data verified and saved successfully.');
    }
    private function parseDate($date, $format)
{
    try {
        return Carbon::createFromFormat($format, $date);
    } catch (\Exception $e) {
        Log::error('Invalid date format: ' . $date . ' Expected format: ' . $format);
        return null; // Return null if the format is incorrect
    }
}
    
}




