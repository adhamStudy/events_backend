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
        'category_id',
        'image',
        'latitude',
        'longitude',
        'is_active',
       
    ];

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
