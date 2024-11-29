<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $table='admins'; 
    protected $fillable = [
       'name', 'email', 'mobile', 'company_type', 'owner_name','role_id','pl_add','pl_days'
    ];
    protected static function boot()
    {
        parent::boot();

        // Listen to the creating event to set the role_id automatically
        static::creating(function ($model) {
            $model->role_id = 3; // Set a default role_id, e.g., 3
            $model->company_type = 'company';
        });
        static::deleting(function ($company) {
            // Delete related employees
            $company->employees()->delete();
    
            // Delete related advance payments
            $company->advancePayments()->delete();
        });
    }
    public function details()
    {
        return $this->hasOne(CompanyDtails::class, 'admin_id', 'id');
    }
    public function employees()
    {
        return $this->hasMany(Employee::class, 'company_id', 'id');
    }
    public function services()
    {
        return $this->hasMany(Services::class, 'id', 'services'); // Assuming 'id' in Service corresponds to values in services array
    }
    public function scopeCompanies($query)
    {
        return $query->where('role_id', 3); // Assuming role_id 3 is for companies
    }
    public function advancePayments()
    {
        return $this->hasMany(AdvancePayment::class);
    }
   
}
