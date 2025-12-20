<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DinnerDate extends Model
{
    protected $fillable = [
        'dinner_group_id',
        'dinner_date',
        'is_closed',
        'notes',
    ];

    protected $casts = [
        'dinner_date' => 'date',
        'is_closed' => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(DinnerGroup::class, 'dinner_group_id');
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(DinnerAvailability::class);
    }
}
