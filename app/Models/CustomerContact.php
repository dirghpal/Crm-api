<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerContact extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'phone',
        'designation',
        'is_primary',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
