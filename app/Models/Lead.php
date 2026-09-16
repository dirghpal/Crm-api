<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{

    protected $fillable = [
        'customer_id',
        'assigned_to',
        'name',
        'email',
        'phone',
        'company',
        'source',
        'status',
        'is_converted',
        'notes',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
