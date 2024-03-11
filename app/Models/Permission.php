<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role;

class Permission extends Model
{
    use HasFactory;
    protected $table = 'permission_db';
    protected $fillable = ['name'];

    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_has_permission');
    }
}
