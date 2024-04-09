<?php

namespace App\Http\Livewire;

use App\Models\Room;
use App\Models\User;
use App\Services\Command\RoomsService;
use Livewire\Component;

class Users extends Component
{
    public function message($userId, RoomsService $service)
    {
        $authenticatedUserId = auth()->id();
        $existingRoom = Room::existingRoom($authenticatedUserId, $userId)->first();

        if ($existingRoom) {
            return redirect()->route('chat', ['roomId' => $existingRoom->id]);
        }

        $createdRoom = $service->createRoom($authenticatedUserId, $userId);

        return redirect()->route('chat', ['roomId' => $createdRoom->id]);
    }

    public function render()
    {
        return view('livewire.users', ['users' => User::where('id', '!=', auth()->id())->get()]);
    }
}
