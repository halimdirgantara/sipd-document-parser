<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entities extends Model
{
    protected $fillable = [
        'affair',
        'sector',
        'organization',
        'sub_organization'
    ];

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'entity_id');
    }
}
