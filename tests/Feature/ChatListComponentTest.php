<?php

namespace Tests\Feature;

use App\Http\Livewire\Chat\ChatList;
use App\Models\User;
use App\Models\Room;
use Livewire\Livewire;
use Tests\TestCase;

class ChatListComponentTest extends TestCase
{
    /** @test */
    public function rooms_are_correctly_displayed_with_corresponding_users()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ChatList::class)
            ->assertSee('No active chats');

        $room = Room::create([
            'sender_id' => $user->id,
            'receiver_id' => User::factory()->create()->id,
        ]);

        Livewire::actingAs($user)
            ->test(ChatList::class)
            ->assertSee($room->getReceiver()->name);
    }
}
