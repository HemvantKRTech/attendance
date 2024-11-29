<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDetails extends Model
{
    use HasFactory;
    protected $table = 'admin_details';
    protected $fillable = [
        'fathername',
        'admin_id', // Link to the Employee
        'gender',
        'aadhar_no',
        'mobile',
        'ac_no',
        'bank_name',
        'ifsc_code',
        'esic_no',
        'epf_no',
        'state_id', // Ensure this matches your foreign key
        'distt_id', // Ensure this matches your foreign key
        'city_id', // Ensure this matches your foreign key
        'location',
        // 'nationality',
        'employee_code',  // Added
        'department',     // Added
        'designation',
        'nationality',
        'employment_type',
        'date_of_joining',
        'date_of_releiving'


    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
    
            // $model->nationality = 'Indian';
           
        });
    }
    public function employee()
{
    return $this->belongsTo(Employee::class, 'admin_id', 'id');
}
public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    // District Relationship
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }
}
