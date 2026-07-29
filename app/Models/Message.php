<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['room_id', 'user_id', 'name', 'body', 'image_path'];

    public function imageUrls()
    {
        if (!$this->image_path) return [];

        $paths = json_decode($this->image_path, true);

        if (is_array($paths)) {
            return array_map(fn($p) => asset('storage/' . $p), $paths);
        }

        return [asset('storage/' . $this->image_path)];
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
