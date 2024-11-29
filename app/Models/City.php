<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table="city";
    use HasFactory;
    public function company()
    {
        return $this->hasMany(Company::class, 'city_id', 'id');
    }
}
