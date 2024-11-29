<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;
    protected $table = 'admin_details';

    // Define the fields that are mass assignable
    protected $fillable = [
        'basic_salary',
        'pf_basic',
        'hra',
        'allowance',
        'lwf',
        'deduction',
        'conveyance',
        'actual_salary',
        'ovr_time_rate'
        // other existing fillable properties
    ];
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
