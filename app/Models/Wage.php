<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wage extends Model
{
    use HasFactory;
    protected $table = 'wages';
    protected $fillable = ['skill_level', 'amount', 'is_active'];
}
