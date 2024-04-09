<?php

namespace App\Services\Command;

use App\Models\Room;

class RoomsService
{
    public function createRoom(int $authenticatedUserId, int $userId): Room
    {
        $room = Room::create([
            'sender_id' => $authenticatedUserId,
            'receiver_id' => $userId,
        ]);

        return $room;
    }

    public function deleteByUser(Room $room, $userId): void
    {
        $room->messages()->each(function($message) use($userId){
            if($message->sender_id===$userId){
                $message->update(['sender_deleted_at'=>now()]);
            }
            elseif($message->receiver_id===$userId){
                $message->update(['receiver_deleted_at'=>now()]);
            }
        } );

        $receiverAlsoDeleted = $room->messages()
            ->where(function ($query) use($userId){
                $query->where('sender_id',$userId)
                    ->orWhere('receiver_id',$userId);
            })->where(function ($query) use($userId){
                $query->whereNull('sender_deleted_at')
                    ->orWhereNull('receiver_deleted_at');
            })->doesntExist();

        if ($receiverAlsoDeleted) {
            $room->forceDelete();
        }
    }
}
