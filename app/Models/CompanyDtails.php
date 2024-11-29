<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDtails extends Model
{
    use HasFactory;
    protected $table = 'admin_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'type',
        'owner_name',
        'address',
        'city',
        'distt',
        'state',
        'gst_no',
        'pan_no',
        'aadhar_no',
        'udyam_no',
        'cin_no',
        'epf_no',
        'esic_no',
        'bank_name',
        'ac_no',
        'ifs_code',
        'city_id', 'state_id', 'district_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */


    /**
     * Get the admin that owns the details.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'admin_id', 'id');
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
        return $this->belongsTo(District::class, 'distt', 'id');
    }
   
}
