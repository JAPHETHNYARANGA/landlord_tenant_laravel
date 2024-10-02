<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Properties extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'rooms',
        'price',
        'type',
        'status',
        'landlord_id',
    ];

    public function landlord()
    {
        return $this->belongsTo(Landlord::class);
    }

    // One property can have many tenants
    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

}
