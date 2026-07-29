<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\Room;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

#[Layout('layouts.guest')]

class ChatRoom extends Component
{
    use WithFileUploads;

    public Room $room;

    public string $body = '';

    public string $name = '';

    public bool $isCreator = false;

    public $images = [];

    public $editingMessageId = null;

    public $editBody = '';

    public function mount(Room $room)
    {
        $this->room = $room;
        $this->name = session('chat_name', 'Guest');
        $this->isCreator = session('creator_token_' . $room->room_id) === $room->creator_token;
    }

    protected function rules()
    {
        return [
            'body' => 'required_without:images|string|max:1000',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|max:5120',
        ];
    }

    public function sendMessage($body = null)
    {
        if ($body !== null) {
            $this->body = $body;
        }

        $this->validate();

        $paths = [];

        if ($this->images) {
            foreach ($this->images as $image) {
                $paths[] = $image->store('chat-images', 'public');
            }
        }

        $this->room->messages()->create([
            'name' => $this->name,
            'body' => $this->body,
            'user_id' => auth()->id(),
            'image_path' => !empty($paths) ? json_encode($paths) : null,
        ]);

        $this->body = '';
        $this->images = [];
    }

    public function editMessage($messageId)
    {
        $message = Message::findOrFail($messageId);

        if ($message->name !== $this->name) return;
        if ($message->created_at->diffInMinutes(now()) >= 10) return;

        $this->editingMessageId = $messageId;
        $this->editBody = $message->body;
    }

    public function updateMessage($messageId, $body = null)
    {
        $message = Message::findOrFail($messageId);

        if ($message->name !== $this->name) return;
        if ($message->created_at->diffInMinutes(now()) >= 10) return;

        if ($body !== null) {
            $message->body = $body;
        } else {
            $message->body = $this->editBody;
        }

        $message->save();

        $this->editingMessageId = null;
        $this->editBody = '';
    }

    public function cancelEdit()
    {
        $this->editingMessageId = null;
        $this->editBody = '';
    }

    public function deleteMessage($messageId)
    {
        $message = Message::findOrFail($messageId);

        if ($message->name !== $this->name) return;
        if ($message->created_at->diffInMinutes(now()) >= 10) return;

        if ($message->image_path) {
            $paths = json_decode($message->image_path, true);
            if (is_array($paths)) {
                foreach ($paths as $path) {
                    Storage::disk('public')->delete($path);
                }
            }
        }

        $message->delete();
    }

    public function deleteRoom()
    {
        if (! $this->isCreator) {
            return;
        }

        foreach ($this->room->messages as $message) {
            if ($message->image_path) {
                $paths = json_decode($message->image_path, true);
                if (is_array($paths)) {
                    foreach ($paths as $path) {
                        Storage::disk('public')->delete($path);
                    }
                } else {
                    Storage::disk('public')->delete($message->image_path);
                }
            }
        }

        $this->room->delete();

        return $this->redirect(route('home'), navigate: true);
    }

    public function getRoomMessages()
    {
        return $this->room->messages()->latest()->take(100)->get();
    }

    public function getParticipants()
    {
        return $this->room->messages()
            ->select('name')
            ->distinct()
            ->where('name', '!=', $this->name)
            ->pluck('name')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.chat-room', [
            'messages' => $this->getRoomMessages(),
            'participants' => $this->getParticipants(),
        ]);
    }
}
