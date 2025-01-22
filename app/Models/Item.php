<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    protected $fillable = [
        'sub_activity_id',
        'account_code',
        'name',
        'quantity',
        'unit',
        'price',
        'tax',
        'total'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function subActivity(): BelongsTo
    {
        return $this->belongsTo(SubActivity::class);
    }
}
