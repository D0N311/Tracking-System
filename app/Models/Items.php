<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    use HasFactory;
    protected $table = 'items_db';
    protected $fillable = [
        'name',
        'item_type',
        'stock',
        'model_number',
        'image_link',
        'item_status',
        'confirmed_at',
        'under_company',
        'owned_by',
    ];
}
