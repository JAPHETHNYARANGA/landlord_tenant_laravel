<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Tenant extends Model
{
    use HasApiTokens,HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
        'password',
        'property_id', 
        'house_no'
    ];

    // Each tenant belongs to one property
    public function properties()
    {
        return $this->belongsTo(Properties::class);
    }
}
