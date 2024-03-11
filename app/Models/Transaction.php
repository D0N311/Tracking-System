<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transaction_db';
    protected $fillable = [
        'transaction_id',
        'ship_to',
        'ship_from',
        'approved_at',
        'shipped_at',
        'image_link',
        'courier_name',
        'delivery_at',
        'registered_by',
    ];
}
