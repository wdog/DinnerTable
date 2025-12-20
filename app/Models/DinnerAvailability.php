<?php

namespace App\Models;

use App\Enums\DinnerAvailabilityStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DinnerAvailability extends Model
{
    //
    protected $fillable = [
        'dinner_date_id',
        'user_id',
        'status',
        'can_host',
        'note',
    ];

    protected $casts = [
        'status' => DinnerAvailabilityStatus::class,
        'can_host' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function (self $model) {
            if ($model->status !== DinnerAvailabilityStatus::AVAILABLE) {
                $model->can_host = false;
            }
        });
    }

    public function dinnerDate(): BelongsTo
    {
        return $this->belongsTo(DinnerDate::class, 'dinner_date_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
