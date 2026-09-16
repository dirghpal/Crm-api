<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Followup extends Model
{

    protected $table = 'follow_ups';

    protected $fillable = [
        'lead_id',
        'customer_id',
        'assigned_to',
        'follow_up_at',
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
