<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
   protected $fillable = ['user_id', 'provider_id', 'title', 'body', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
