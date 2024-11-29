<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wage;
use App\Http\Resources\Admin\Wages\WagesCollection;
class WagesController extends Controller
{
    public function index(Request $request){
       
        
        if ($request->wantsJson()) {
            //dd($request->all());
            $datas = Wage::where('is_active', 1)
            ->orderBy('created_at', 'desc')   
            ->select([
                'id',           
                'skill_level as name',
                'amount',
                'is_active'
            ]);

            $request->merge(['recordsTotal' => $datas->count(), 'length' => $request->length]);
            $datas = $datas->limit($request->length)->offset($request->start)->get();

            return response()->json(new WagesCollection($datas));
           
        }
        return view('admin.wages.list');
    }
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'unskilled_wage' => 'required|numeric|min:0',
            'semi_skilled_wage' => 'required|numeric|min:0',
            'skilled_wage' => 'required|numeric|min:0',
        ]);

        // Update wage for UNSKILLED workers
        $this->updateWage('UNSKILLED', $request->unskilled_wage);

        // Update wage for SEMI-SKILLED workers
        $this->updateWage('SEMI-SKILLED', $request->semi_skilled_wage);

        // Update wage for SKILLED workers
        $this->updateWage('SKILLED', $request->skilled_wage);

        // Redirect back with a success message
        return redirect()->back()->with(['class' => 'success', 'message' => 'Wages Updated successfully.']);
    }

    /**
     * Update the wage for a given skill level.
     * 
     * @param string $skillLevel
     * @param float $newAmount
     */
    private function updateWage($skillLevel, $newAmount)
    {
        // Set previous active wage for this skill level as inactive
        Wage::where('skill_level', $skillLevel)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Create a new wage record
        Wage::create([
            'skill_level' => $skillLevel,
            'amount' => $newAmount,
            'is_active' => true, // Mark the new wage as active
        ]);
    }
    public function create(){
        return view('admin.wages.create');
    }
    public function edit($id){
        $wages=Wage::find($id);
        return view('admin.wages.edit',compact('wages'));
    }



    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'wage' => 'required|numeric|min:0', // Ensure wage is a valid number and not negative
        ]);

        // Find the existing wage record
        $wages = Wage::findOrFail($id); // Fetch the wage record or fail with a 404

        // Set the existing wage record's status to inactive
        $wages->is_active = 'inactive'; // Mark as inactive
        $wages->save(); // Save the changes

        // Create a new wage record with the updated amount
        Wage::create([
            'skill_level' => $wages->skill_level, // Keep the same skill level
            'amount' => $validatedData['wage'], // New wage amount
            'is_active' => 'active', // Set status to active for the new record
        ]);

        // Redirect or return response as necessary
        return redirect()->back()->with(['class' => 'success', 'message' => 'Wages Updated successfully.']);
    }
}
