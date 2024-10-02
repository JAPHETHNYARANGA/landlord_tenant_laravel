<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword as PasswordReset;


class Landlord extends Model implements CanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable, PasswordReset;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
        'password',
    ];

    public function properties()
    {
        return $this->hasMany(Properties::class);
    }
}
