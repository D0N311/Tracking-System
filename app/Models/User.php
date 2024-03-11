<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Role;
use App\Models\Permission;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'phone_number',
        'company_id',
        'activated_at',
        'deactivated_at',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_has_role');
    }
    public function assignRole($role)
    {
        $this->roles()->attach($role);
    }
    public function getRoleNames()
    {
        return $this->roles->pluck('name');
    }
    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }
    public function removeRole($role)
    {
        $role = Role::where('name', $role)->first();
        if ($role) {
            $this->roles()->detach($role->id);
        }
    }



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
