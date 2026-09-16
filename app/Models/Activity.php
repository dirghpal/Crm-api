<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{

    protected $fillable = [
        'lead_id',
        'customer_id',
        'type',
        'title',
        'description',
        'activity_at',
        'status',
        'assigned_to',
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
