<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $createdMessage;

    public User $userToNotify;

    public User $authUser;

    public Room $room;

    public function __construct(Message $createdMessage, User $userToNotify, User $authUser, Room $room)
    {
        $this->createdMessage = $createdMessage;
        $this->userToNotify = $userToNotify;
        $this->authUser = $authUser;
        $this->room = $room;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
