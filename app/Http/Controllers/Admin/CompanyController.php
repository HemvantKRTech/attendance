<?php

namespace App\Http\Controllers\Admin;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyDtails;
use App\Models\Temp_companydetail;
use App\Models\State;
use App\Models\City;
use App\Models\District;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Services;
use App\Models\Company;
use App\Http\Resources\Admin\Company\CompanyCollection;
use App\Imports\CompanyImport;
use Maatwebsite\Excel\Facades\Excel;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    if ($request->wantsJson()) {
       
        $datas = Company::orderBy('admins.created_at', 'desc')
    ->where('admins.role_id', 3) // Filter where role_id is 3
    ->whereNotIn('admins.id', [1]) // Exclude admins with id 1
    ->join('roles', 'roles.id', '=', 'admins.role_id') // Join with roles table
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
    
    return view('admin.company.list');
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::whereNotIn('id',[1])->select(['id','name'])->get()->pluck('name','id')->toArray();
        // dd($roles);
        $state=State::all();
        // dd($state);
       
        return view('admin.company.create', compact('roles','state'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // dd($request->all());
    // Validate the request data
    $validatedData = $request->validate([
        'company_name' => 'required',
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('admins')->where(function ($query) {
                return $query->where('company_type', 'company');
            }),
        ],
        'contact_no' => 'required|string|max:10|regex:/^\d{10}$/',
        'type' => 'required|string|max:255',
        'owner_name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'city' => 'required|exists:city,id',
        'distt' => 'required|exists:district,id',
        'state' => 'required|exists:state,id',
        'gst_no' => [
            'required',
            'string',
            // //'size:15',
            Rule::unique('admin_details', 'gst_no')->where(function ($query) {
                return $query->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('admins')
                        ->whereColumn('admins.id', 'admin_details.admin_id')
                        ->where('admins.company_type', 'company');
                });
            }),
        ],
        'pan_no' => [
            'required',
            'string',
            //'size:10',
            Rule::unique('admin_details', 'pan_no')->where(function ($query) {
                return $query->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('admins')
                        ->whereColumn('admins.id', 'admin_details.admin_id')
                        ->where('admins.company_type', 'company');
                });
            }),
        ],
        'aadhar_no' => [
            'required',
            'string',
            //'size:12',
            Rule::unique('admin_details', 'aadhar_no')->where(function ($query) {
                return $query->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('admins')
                        ->whereColumn('admins.id', 'admin_details.admin_id')
                        ->where('admins.company_type', 'company');
                });
            }),
        ],
        'udyam_no' => 'nullable|string|max:19|unique:admin_details,udyam_no',
        'cin_no' => [
            'nullable',
            'string',
            'required_unless:type,Limited Liability Partnership (LLP)',
            'unique:admin_details,cin_no',
        ],
        'epf_no' => 'nullable|string|max:22|unique:admin_details,epf_no',
        'esic_no' => 'nullable|string|max:22|unique:admin_details,esic_no',
        'bank_name' => 'required|string|max:255',
        'ac_no' => 'required|string|max:20',
        'ifs_code' => 'required|string|max:11',
        'pl_days' => 'required',
        'pl_add' => 'required',

       
    ]);
    // dd($validatedData);

    DB::beginTransaction();

    try {
        // Create the company
        $company = new Company();  // Assuming "Admin" is the Company model
        $company->name = $request->input('company_name');
        // $company->role_id=3;
        $company->email = $request->input('email');
        $company->mobile = $request->input('contact_no');
        // $company->company_type = 'company';  // Set company type explicitly
        $company->owner_name = $request->input('owner_name');
        $company->pl_days = $request->input('pl_days');
        $company->pl_add = $request->input('pl_add');

        $company->save();

        // Create company details
        $companyDetail = new CompanyDtails();  // Assuming "AdminDetail" is the Company Details model
        $companyDetail->admin_id = $company->id;
        // $companyDetail->type = $request->input('type');

        $companyDetail->address = $request->input('address');
        $companyDetail->city_id = $request->input('city');
        $companyDetail->distt_id = $request->input('distt');
        $companyDetail->state_id = $request->input('state');
        $companyDetail->gst_no = $request->input('gst_no');
        $companyDetail->pan_no = $request->input('pan_no');
        $companyDetail->aadhar_no = $request->input('aadhar_no');
        $companyDetail->udyam_no = $request->input('udyam_no');
        $companyDetail->cin_no = $request->input('cin_no');
        $companyDetail->epf_no = $request->input('epf_no');
        $companyDetail->esic_no = $request->input('esic_no');
        $companyDetail->bank_name = $request->input('bank_name');
        $companyDetail->ac_no = $request->input('ac_no');
        $companyDetail->ifs_code = $request->input('ifs_code');
        $companyDetail->save();

        DB::commit();

        return redirect()->back()->with(['class' => 'success', 'message' => 'Company Created successfully.']);
    } catch (\Exception $e) {
        dd($e);
        DB::rollBack();
        return redirect()->back()->with(['class' => 'error', 'message' => 'Failed to create Company. Please try again later.']);
    }
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = Company::findOrFail($id);
    $adminDetail = CompanyDtails::where('admin_id', $admin->id)->first();
    $state=State::find($adminDetail->state_id);

    $state=$state->state_title;
    // dd($state);

    $district=District::find($adminDetail->distt_id);
    $district=$district->district_title;

    $city=City::find($adminDetail->city_id);
    $city=$city->name;
    return view('admin.company.view',compact('admin', 'adminDetail','state','district','city'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $company = Company::with(['details.city', 'details.state', 'details.district'])
                   ->find($id);
                   $states = State::pluck('state_title', 'id')->toArray();
                   $city = City::pluck('name', 'id')->toArray(); 
                //    dd($city);

                   $district = District::pluck('district_title', 'id')->toArray(); 
                //    dd($company->services);
                
        return view('admin.company.edit',compact('company','states','city','district'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'company_name' => 'required|string|max:255|unique:admins,name,' . $id,
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('admins')->where(function ($query) {
                return $query->where('company_type', 'company');
            })->ignore($id) // Ignore current record for update
        ],
        'contact_no' => 'required|string|max:10|regex:/^\d{10}$/',
        'type' => 'required|string|max:255',
        'owner_name' => 'required|string|max:255',
        'address' => 'required|string',
        'city' => 'required|exists:city,id',
        'distt' => 'required|exists:district,id',
        'state' => 'required|exists:state,id',
        'gst_no' => [
            'required',
            'string',
           
            Rule::unique('admin_details', 'gst_no')->where(function ($query) {
                return $query->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                             ->from('admins')
                             ->whereColumn('admins.id', 'admin_details.admin_id') // Assuming admin_id is the foreign key
                             ->where('admins.company_type', 'company');
                });
            })
        ],
        'pan_no' => [
            'required',
            'string',
            //'size:10',
            Rule::unique('admin_details', 'pan_no')->where(function ($query) {
                return $query->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                             ->from('admins')
                             ->whereColumn('admins.id', 'admin_details.admin_id') // Assuming admin_id is the foreign key
                             ->where('admins.company_type', 'company');
                });
            })
        ],
        'aadhar_no' => [
            'required',
            'string',
            //'size:12',
            Rule::unique('admin_details', 'aadhar_no')->where(function ($query) {
                return $query->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                             ->from('admins')
                             ->whereColumn('admins.id', 'admin_details.admin_id') // Assuming admin_id is the foreign key
                             ->where('admins.company_type', 'company');
                });
            })
        ],
        'udyam_no' => 'nullable|string|required', // Ensure size is correct
        'cin_no' => [
            'nullable',
            'string',
           
            'required_if:type,Limited Liability Partnership (LLP)', // CIN No is required if the type is 'LLP'
        ],
        'epf_no' => 'nullable|string', // Adjust size as necessary
        'esic_no' => 'nullable|string', // Adjust size as necessary
        'bank_name' => 'required|string|max:255',
        'ac_no' => 'required|string|max:255',
        'ifs_code' => 'required|string|max:255',
        'services'=>'required',
        'monthly_charges'=>'required',
    ]);
    DB::beginTransaction();

    try {
        // Update the company
        $admin = Company::findOrFail($id);
        $admin->name = $request->input('company_name');
        $admin->email = $request->input('email');
        $admin->mobile = $request->input('contact_no');
        $admin->services=json_encode($request->services);
        $admin->monthly_fees=$request->monthly_charges;
        $admin->save();

        // Update company details
        $adminDetail = CompanyDtails::where('admin_id', $id)->first();
        if (!$adminDetail) {
            $adminDetail = new CompanyDtails();
            $adminDetail->admin_id = $id;
        }
        
        $adminDetail->type = $request->input('type');
        $adminDetail->owner_name = $request->input('owner_name');
        $adminDetail->address = $request->input('address');
        $adminDetail->city_id = $request->input('city');
        $adminDetail->district_id = $request->input('distt');
        $adminDetail->state_id = $request->input('state');
        $adminDetail->gst_no = $request->input('gst_no');
        $adminDetail->pan_no = $request->input('pan_no');
        $adminDetail->aadhar_no = $request->input('aadhar_no');
        $adminDetail->udyam_no = $request->input('udyam_no');
        $adminDetail->cin_no = $request->input('cin_no');
        $adminDetail->epf_no = $request->input('epf_no');
        $adminDetail->esic_no = $request->input('esic_no');
        $adminDetail->bank_name = $request->input('bank_name');
        $adminDetail->ac_no = $request->input('ac_no');
        $adminDetail->ifs_code = $request->input('ifs_code');
        $adminDetail->save();
        DB::commit();

        return redirect()->back()->with(['class'=>'success', 'message'=>'Company updated successfully.']);

    } catch (\Exception $e) {
        DB::rollBack();

        return redirect()->back()->with(['class'=>'danger', 'message'=>'Failed to update company. Please try again later.']);
    }
}
public function verifyInsert()
{
    DB::beginTransaction();
    try {
        $existingCompanies = [];
        $newCompanies = [];

        // Fetch temporary companies
        $tempCompanies = Temp_companydetail::all();

        foreach ($tempCompanies as $tempCompany) {
            // Check if the company already exists by some unique identifier (e.g., GST, CIN, etc.)
            $existingCompany = CompanyDtails::where('gst_no', $tempCompany->gst_no)
                ->orWhere('cin_no', $tempCompany->cin_no)
                ->first();

            if ($existingCompany) {
                // Add to existing companies array
                $existingCompanies[] = $tempCompany->company_name;
                continue; // Skip this company as it already exists
            }

            // Get State ID by state name
            $state = State::where('state_title', $tempCompany->state)->first();
            if (!$state) {
                throw new \Exception('State not found: ' . $tempCompany->state);
            }

            // Get District ID by district name and state_id
            $district = District::where('district_title', $tempCompany->distt)
                ->where('state_id', $state->id)
                ->first();
            if (!$district) {
                throw new \Exception('District not found: ' . $tempCompany->distt . ' in state: ' . $tempCompany->state);
            }

            // Get City ID by city name and district_id
            $city = City::where('name', $tempCompany->city)
                ->where('districtid', $district->id)
                ->first();
            if (!$city) {
                throw new \Exception('City not found: ' . $tempCompany->city . ' in district: ' . $tempCompany->distt);
            }

            // Insert new company into Company table
            $company = Company::create([
                'name' => $tempCompany->company_name,
                'email' => $tempCompany->contact_no, // or other relevant fields
                'mobile' => $tempCompany->contact_no
            ]);

            // Insert details into CompanyDetails table with fetched state, district, and city IDs
            CompanyDtails::create([
                'company_id' => $company->id,
                'type' => $tempCompany->type,
                'owner_name' => $tempCompany->owner_name,
                'address' => $tempCompany->address,
                'city_id' => $city->id,
                'district_id' => $district->id,
                'state_id' => $state->id,
                'gst_no' => $tempCompany->gst_no,
                'pan_no' => $tempCompany->pan_no,
                'aadhar_no' => $tempCompany->aadhar_no,
                'udyam_no' => $tempCompany->udyam_no,
                'cin_no' => $tempCompany->cin_no,
                'epf_no' => $tempCompany->epf_no,
                'esic_no' => $tempCompany->esic_no,
                'bank_name' => $tempCompany->bank_name,
                'ac_no' => $tempCompany->ac_no,
                'ifs_code' => $tempCompany->ifs_code
            ]);

            // Add to new companies array
            $newCompanies[] = $tempCompany->company_name;
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Companies verified and inserted successfully.',
            'existing_companies' => $existingCompanies,
            'new_companies' => $newCompanies
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => $e->getMessage() // Return the specific error
        ]);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
    
       
            // Find the company by ID where company_type is 'company'
            $company = Company::where('id', $id)->where('company_type', 'company')->first();
           $companydetails=CompanyDtails::where('id',$id)->get();
            if ($company) {
                // Delete the related company details
                $company->delete();
                $companydetails->delete();
    
                // Delete the company itself
               
    
                return response()->json(['message'=>'Company deleted Successfully ...', 'class'=>'success']);
            }
    
            // If company is not found or company_type is not 'company'
            return response()->json(['message'=>'Whoops, looks like something went wrong ! Try again ...', 'class'=>'error']);
       
    }
    public function import(){
        $tempCompanies = Temp_companydetail::paginate(10);
        return view('admin.company.import', compact('tempCompanies'));
    }
    public function storeimport(Request $request){
        $request->validate([
            'company_excel' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        if ($request->hasFile('company_excel')) {
            $file = $request->file('company_excel');

            try {
                Excel::import(new CompanyImport, $file);

                return redirect()->back()->with(['class' => 'success', 'message' => 'Company data imported successfully.']);
            } catch (\Exception $e) {
                return redirect()->back()->with(['class' => 'danger', 'message' => 'Failed to import company data. Please try again later.']);
            }
        }

        return redirect()->back()->with(['class' => 'danger', 'message' => 'No file was uploaded.']);
  
    }
}
