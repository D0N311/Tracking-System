<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'char';

    protected $fillable = ['id'];
}
