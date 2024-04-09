<?php

namespace App\Services\Command;

use App\Models\Message;

class MessagesService
{
    public function messageRead(int $roomId): void
    {
        Message::where('room_id', $roomId)
            ->where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function createMessage(int $roomId, int $senderId, int $receiverId, string $body): Message
    {
        $message = Message::create([
            'room_id' => $roomId,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'body' => htmlspecialchars($body),
        ]);

        return $message;
    }

    public function loadMessages(int $userId, int $roomId, int $count, int $paginate)
    {
        return Message::activeRoomMessages($userId, $roomId)
            ->skip($count - $paginate)
            ->take($paginate)
            ->get();
    }
}
