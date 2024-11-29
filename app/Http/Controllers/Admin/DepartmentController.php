<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Department\DepartmentCollection;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Department;


class DepartmentController extends Controller
{
    public function index(Request $request){
        if ($request->wantsJson()) {
       
            $datas = Department::with(['company' => function ($query) {
                $query->select('id', 'name'); // Select 'id' and 'name' from the company table
            }]);
            // dd($datas);
            $request->merge(['recordsTotal' => $datas->count(), 'length' => $request->length]);
            $datas = $datas->limit($request->length)->offset($request->start)->get();
      
    
        return response()->json(new DepartmentCollection($datas));
        }
        return view('admin.department.list');
    }
    public function create(){
        $companies = Company::
        where('company_type', 'company')
        ->get();
        // dd($companies);
        return view('admin.department.create',compact('companies'));
    }
    public function store(Request $request)
    {
        // Validate the input
        $validatedData = $request->validate([
            'company_id' => 'required|exists:admins,id',
            'name' => 'required|string|max:255|unique:departments,name,NULL,id,company_id,' . $request->company_id,
        ]);

        // Create the department
        Department::create([
            'company_id' => $validatedData['company_id'],
            'name' => $validatedData['name'],
        ]);

        return redirect()->back()->with(['class' => 'success', 'message' => 'Department created successfully.']);
    }
    public function edit($id){
        $department=Department::with('company')->find($id);
        // dd($department);
        $companies = Company::
        where('company_type', 'company')
        ->get();
        return view('admin.department.edit',compact('department','companies'));
    }
    public function update(Request $request,  $id)
    {
        // Validate the input
        $department=Department::with('company')->find($id);
        // dd($department);
        $validatedData = $request->validate([
            'company_id' => 'required|exists:admins,id',
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id . ',id,company_id,' . $request->company_id,
        ]);
        // dd($validatedData);

        // Update the department
        $department->update([
            'company_id' => $validatedData['company_id'],
            'name' => $validatedData['name'],
        ]);

        return redirect()->back()->with(['class' => 'success', 'message' => 'Department Updated successfully.']);
    }
    public function show($id){
        $department=Department::with('company')->find($id);
        // dd($department);
        return view('admin.department.view',compact('department'));
    }
    public function destroy($id){
        $department=Department::find($id);
        // dd($department);
        $department->delete();
        return redirect()->back()->with(['class' => 'success', 'message' => 'Department deleted successfully.']);
    }
    public function getDepartmentsByCompany(Request $request)
{
    // Validate the company_id input
    $company_id = $request->input('company_id');
    
    // Get the departments for the selected company
    $departments = Department::where('company_id', $company_id)->select('id', 'name')->get();
    
    // Return the response as JSON
    return response()->json([
        'departments' => $departments
    ]);
}

   
}
