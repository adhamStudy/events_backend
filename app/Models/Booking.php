<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'status',
        'booking_date',
    ];

    /**
     * علاقة الحجز بالمستخدم (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة الحجز بالفعالية (Event)
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
