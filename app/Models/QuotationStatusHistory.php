<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationStatusHistory extends Model
{

    protected $fillable = [
        'quotation_id',
        'status',
        'comment',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
