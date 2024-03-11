<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $table = 'company_db';
    protected $fillable = [
        'company_name',
        'description',
        'location',
        'admin_id',
    ];
}
