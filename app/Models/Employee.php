<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $table='admins'; 
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'gender',
        'date_of_birth',
        'company_id',
        'role_id',
        'ovr_time_rate'
    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // $model->role_id = 4; 
            $model->company_type = 'employee';
           
        });
    }
    public function company()
    {
        return $this->belongsTo(Employee::class, 'company_id', 'id'); 
    }
    public function employeedetail()
{
    return $this->hasOne(EmployeeDetails::class, 'admin_id', 'id');
}
    public function scopeEmployees($query)
    {
        return $query->where('role_id', 4); 
    }
    public function tempMonthlySalaries()
    {
        return $this->hasMany(TempMonthlySalary::class, 'admin_id');
    }
    public function salaryDetails()
    {
        return $this->hasMany(MonthlySalaryDetail::class, 'employee_id');
    }
}
