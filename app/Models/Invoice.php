<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'quotation_id',
        'deal_id',
        'lead_id',
        'customer_id',
        'assigned_to',
        'invoice_number',
        'invoice_date',
        'due_date',
        'amount',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'status',
        'notes',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

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
        return $this->hasMany(InvoiceStatusHistory::class);
    }
}
