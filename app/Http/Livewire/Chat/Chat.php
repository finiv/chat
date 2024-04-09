<?php

namespace App\Http\Livewire\Chat;

use App\Models\Room;
use App\Services\Command\MessagesService;
use Livewire\Component;

class Chat extends Component
{
    public int $roomId;

    public Room $selectedRoom;

    public function mount(): void
    {
        $this->selectedRoom = Room::findOrFail($this->roomId);

        app(MessagesService::class)->messageRead($this->selectedRoom->id);
    }

    public function render()
    {
        return view('livewire.chat.chat');
    }
}
