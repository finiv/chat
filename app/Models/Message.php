<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
        'sender_id',
        'receiver_id',
        'room_id',
        'read_at',
        'receiver_deleted_at',
        'sender_deleted_at',
    ];

    protected $dates = ['read_at', 'receiver_deleted_at', 'sender_deleted_at'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function isRead(): bool
    {
        return $this->read_at != null;
    }

    public function scopeCountByRoom(Builder $query, int $roomId): int
    {
        return $query->where('room_id', $roomId)->count();
    }

    public function scopeActiveRoomMessages(Builder $query, int $userId, int $roomId): Builder
    {
        return $query->where(function ($query) use ($userId, $roomId) {
            $query->where('sender_id', $userId)
                ->whereNull('receiver_deleted_at')
                ->where('room_id', $roomId);
        })
            ->orWhere(function ($query) use ($userId, $roomId) {
                $query->where('receiver_id', $userId)
                    ->whereNull('sender_deleted_at')
                    ->where('room_id', $roomId);
            });
    }
}
