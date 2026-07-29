<?php

namespace App\Livewire;

use App\Models\Room;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;


#[Layout('layouts.guest')]

class RoomIndex extends Component
{
    public string $name = '';

    public string $joinRoomId = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:50',
            'joinRoomId' => 'required|string|size:6',
        ];
    }

    public function createRoom()
    {
        $this->validateOnly('name');

        session(['chat_name' => $this->name]);

        do {
            $roomId = strtoupper(Str::random(6));
        } while (Room::where('room_id', $roomId)->exists());

        $creatorToken = Str::random(60);

        $room = Room::create([
            'room_id' => $roomId,
            'creator_token' => $creatorToken,
        ]);

        session(['creator_token_' . $roomId => $creatorToken]);

        return $this->redirect(route('chat.room', $roomId), navigate: true);
    }

    public function joinRoom()
    {
        $this->validate();

        session(['chat_name' => $this->name]);

        $room = Room::where('room_id', strtoupper($this->joinRoomId))->first();

        if (!$room) {
            $this->addError('joinRoomId', 'Room not found');
            return;
        }

        return $this->redirect(route('chat.room', $room->room_id), navigate: true);
    }

    public function render()
    {
        return view('livewire.room-index');
    }
}
