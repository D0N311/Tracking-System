<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Permission;

class Role extends Model
{
    use HasFactory;
    protected $table = 'role_db';
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_role');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permission');
    }

    public function givePermissionTo($permission)
    {
        $permission = Permission::where('name', $permission)->firstOrFail();
        $this->permissions()->attach($permission);
    }
}
