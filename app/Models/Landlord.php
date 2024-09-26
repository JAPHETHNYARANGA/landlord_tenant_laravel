<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Landlord extends Model
{
    use HasApiTokens,HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
        'password',
    ];
}
