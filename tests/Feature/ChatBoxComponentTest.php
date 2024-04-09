<?php

namespace Tests\Feature;

use App\Http\Livewire\Chat\ChatBox;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class ChatBoxComponentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_send_message()
    {
        Notification::fake();

        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $room = Room::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        Livewire::actingAs($sender)
            ->test(ChatBox::class, ['selectedRoom' => $room])
            ->set('body', 'Test message')
            ->call('sendMessage');

        $this->assertCount(1, Message::all());
        $this->assertEquals('Test message', Message::first()->body);
    }

    /** @test */
    public function can_verify_users_in_room()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $room = Room::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        Livewire::actingAs($sender)
            ->test(ChatBox::class, ['selectedRoom' => $room])
            ->assertSee($receiver->email);
    }
}
