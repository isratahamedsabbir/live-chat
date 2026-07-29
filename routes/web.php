<?php

use App\Livewire\ChatRoom;
use App\Livewire\RoomIndex;
use App\Models\Room;
use Illuminate\Support\Facades\Route;

Route::get('/', RoomIndex::class)->name('home');

Route::get('/room/{room:room_id}', ChatRoom::class)->name('chat.room');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
