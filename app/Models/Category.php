<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class Category extends Model
// {
//     protected $fillable = [
//         'name',
//         'icon'

//     ];

//     public function favoritedByUsers()
//     {
//         return $this->belongsToMany(User::class, 'user_favorite_categories');
//     }
//     public function events()
//     {
//         return $this->hasMany(Event::class);
//     }
// }


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
    ];

    /**
     * Get all events for this category
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Users who favorited this category
     */
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorite_categories');
    }
}
