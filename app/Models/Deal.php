<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{

    protected $fillable = [
        'lead_id',
        'customer_id',
        'assigned_to',
        'title',
        'amount',
        'stage',
        'probability',
        'expected_close_at',
        'status',
        'notes',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
