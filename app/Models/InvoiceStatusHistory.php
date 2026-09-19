<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceStatusHistory extends Model
{

    protected $fillable = [
        'invoice_id',
        'status',
        'comment',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
