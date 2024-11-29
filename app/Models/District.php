<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table="district";
    use HasFactory;
    public function company()
    {
        return $this->hasMany(Company::class, 'state', 'id');
    }
}
