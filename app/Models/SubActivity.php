<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubActivity extends Model
{
    protected $fillable = [
        'activity_id',
        'name',
        'funding_source',
        'location',
        'execution_time',
        'target_group',
        'current_year',
        'current_year_allocation',
        'previous_year',
        'previous_year_allocation',
        'next_year',
        'next_year_allocation'
    ];

    protected $casts = [
        'current_year_allocation' => 'decimal:2',
        'previous_year_allocation' => 'decimal:2',
        'next_year_allocation' => 'decimal:2'
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
