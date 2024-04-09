<?php

namespace Tests\Feature;

use App\Http\Livewire\Users;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UsersComponentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_message_user()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        Livewire::actingAs($sender)
            ->test(Users::class)
            ->call('message', $receiver->id)
            ->assertRedirect(route('chat', ['roomId' => 1]));
    }

    /** @test */
    public function redirected_to_existing_room_if_exists()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $roomId = $sender->rooms()->create(['receiver_id' => $receiver->id])->id;

        Livewire::actingAs($sender)
            ->test(Users::class)
            ->call('message', $receiver->id)
            ->assertRedirect(route('chat', ['roomId' => $roomId]));
    }

    /** @test */
    public function users_are_loaded_correctly()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        Livewire::actingAs($sender)
            ->test(Users::class)
            ->assertSee($receiver->name); // Assuming there is a 'name' attribute in the User model
    }
}
