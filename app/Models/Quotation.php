<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{

    protected $fillable = [
        'deal_id',
        'lead_id',
        'customer_id',
        'assigned_to',
        'quotation_number',
        'title',
        'amount',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'valid_until',
        'status',
        'notes',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

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

    public function statusHistories()
    {
        return $this->hasMany(QuotationStatusHistory::class);
    }
}
