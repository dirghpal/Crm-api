<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address',
        'city',
        'state',
        'pincode',
        'status',
    ];


    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
