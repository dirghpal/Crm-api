<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealStageHistory extends Model
{

    protected $fillable = [
        'deal_id',
        'stage',
        'probability',
        'comment',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
