<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetCodePassword extends Model
{
    use HasFactory;
    protected $table = 'reset_code_passwords';
    protected $primaryKey = 'code';
    protected $fillable = [
        'email',
        'code',
        'created_at',
    ];
    public function isExpire()
    {
        if (now() > $this->created_at->addHour()) {
            $this->delete();
            return true;
        }

        return false;
    }
    public $incrementing = false;
}
