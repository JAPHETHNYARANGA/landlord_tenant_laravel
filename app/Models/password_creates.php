<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class password_creates extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'token',
        'user_type'
    ];
}
