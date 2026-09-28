<?php

namespace App\Models;

use App\Models\Quotation;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'item_name',
        'quantity',
        'price',
        'total_amount',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

}
