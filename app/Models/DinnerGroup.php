<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DinnerGroup extends Model
{
    protected $table = 'dinner_groups';

    protected $fillable = [
        'name',
        'slogan',
        'image',
        'group_code',
        'created_by',
    ];

    /**
     * Get the user who created the team.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all members of the dinner group.
     */
    public function members(): HasMany
    {
        return $this->hasMany(User::class, 'dinner_group_id');
    }
}
