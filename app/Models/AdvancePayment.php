<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancePayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'company_id',
        'amount',
        'emi_amount',
        'total_emi_count',
        'pending_emi_count',
        'payment_type',
        'status',
        'date_taken',
        'description',
        'interest',
        'emi_month',
        'emi_year',

        'total_payable_amount'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Scope for active advances
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for EMI-based advances
    public function scopeEMI($query)
    {
        return $query->where('payment_type', 'emi');
    }
}
