<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DinnerDate extends Model
{
    protected $fillable = [
        'dinner_group_id',
        'dinner_date',
    ];

    protected $casts = [
        'dinner_date' => 'date',
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
