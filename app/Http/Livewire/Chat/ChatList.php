<?php

namespace App\Http\Livewire\Chat;

use App\Models\Room;
use App\Services\Command\RoomsService;
use Livewire\Component;

class ChatList extends Component
{
    public $roomId;

    public $selectedRoom;

    protected $listeners = ['refresh' => '$refresh'];

    public function deleteByUser($id)
    {
        app(RoomsService::class)->deleteByUser(Room::findOrFail(decrypt($id)), auth()->id());
        // alternatively could be implemented by __construct(private readonly RoomsService $service)
        // and $this->service->deleteByUser(...

        return redirect(route('chat.index'));
    }

    public function render()
    {
        $user = auth()->user();

        return view('livewire.chat.chat-list', ['rooms' => $user->rooms()->latest('updated_at')->get()]);
    }
}
