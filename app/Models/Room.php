<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['room_id', 'creator_token'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
