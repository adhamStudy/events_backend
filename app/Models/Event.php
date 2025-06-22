<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_time',
        'end_time',
        'city_id',
        'capacity',
        'available_seats',
        'category_id',
        'image',
        'latitude',
        'longitude',
        'is_active',
       
    ];
    protected static function booted()
    {
        static::creating(function ($event) {
            // عند الإنشاء، نعين available_seats = capacity
            $event->available_seats = $event->capacity;
        });}

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function bookings()
{
    return $this->hasMany(Booking::class);
}

}
