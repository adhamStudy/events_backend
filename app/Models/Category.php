<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'icon'

    ];

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_favorite_categories');
    }
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
