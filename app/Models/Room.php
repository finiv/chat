<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['receiver_id', 'sender_id'];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function getReceiver(): User
    {
        if ($this->sender_id === auth()->id()) {
            return User::firstWhere('id', $this->receiver_id);
        }

        return User::firstWhere('id', $this->sender_id);
    }

    public function scopeWhereNotDeleted($query)
    {
        $userId = auth()->id();

        return $query->where(function ($query) use ($userId) {
            $query->whereHas('messages', function ($query) use ($userId) {
                $query->where(function ($query) use ($userId) {
                    $query->where('sender_id', $userId)
                        ->whereNull('sender_deleted_at');
                })->orWhere(function ($query) use ($userId) {
                    $query->where('receiver_id', $userId)
                        ->whereNull('receiver_deleted_at');
                });
            })
                ->orWhereDoesntHave('messages');
        });
    }

    public function isLastMessageReadByUser(): bool
    {
        $user = Auth()->User();
        $lastMessage = $this->messages()->latest()->first();

        if ($lastMessage) {
            return $lastMessage->read_at !== null && $lastMessage->sender_id == $user->id;
        }

        return false;
    }

    public function unreadMessagesCount(): int
    {
        return Message::where('room_id', '=', $this->id)
            ->where('receiver_id', auth()->user()->id)
            ->whereNull('read_at')->count();
    }

    public function scopeExistingRoom(Builder $query, int $authenticatedUserId, int $userId): Builder
    {
        return $query->where(function ($query) use ($authenticatedUserId, $userId) {
            $query->where('sender_id', $authenticatedUserId)
                ->where('receiver_id', $userId);
            })
            ->orWhere(function ($query) use ($authenticatedUserId, $userId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', $authenticatedUserId);
            });
    }
}
