<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'entity_id',
        'name'
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entities::class, 'entity_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
