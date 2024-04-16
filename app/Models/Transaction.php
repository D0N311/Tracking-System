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
        'description',
        'transaction_from',
        'approved_at',
        'shipped_at',
        'image_link',
        'courier_name',
        'delivery_at',
        'registered_by',
    ];

    public function ship_to()
    {
        return $this->belongsTo(User::class, 'ship_to', 'id');
    }
    public function registered_by()
    {
        return $this->belongsTo(User::class, 'registered_by', 'id');
    }

}
