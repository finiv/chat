<?php

namespace App\Http\Livewire\Chat;

use App\Models\Room;
use App\Models\Message;
use App\Notifications\MessageRead;
use App\Notifications\MessageSent;
use App\Services\Command\MessagesService;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class ChatBox extends Component
{
    public Room $selectedRoom;

    public string $body;

    public Collection $loadedMessages;

    public int $paginate_var = 10;

    protected $listeners = ['loadMore'];

    public function getListeners(): array
    {
        $auth_id = auth()->user()->id;

        return [
            'loadMore',
            "echo-private:users.{$auth_id},.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'broadcastedNotifications',
        ];
    }

    public function broadcastedNotifications($event): void
    {
        if ($event['type'] == MessageSent::class) {
            if ($event['room_id'] == $this->selectedRoom->id) {
                $this->dispatchBrowserEvent('scroll-bottom');
                $newMessage = Message::find($event['message_id']);
                $this->loadedMessages->push($newMessage);
                $newMessage->update(['read_at' => now()]);
                $this->selectedRoom->getReceiver()
                    ->notify(new MessageRead($this->selectedRoom->id)); // can be also placed in event() like line 79
            }
        }
    }

    public function loadMore(): void
    {
        $this->paginate_var += 10;
        $this->loadMessages();
        $this->dispatchBrowserEvent('update-chat-height');
    }

    public function loadMessages(): Collection
    {
        $this->loadedMessages = app(MessagesService::class)->loadMessages(
            auth()->id(),
            $this->selectedRoom->id,
            Message::countByRoom($this->selectedRoom->id),
            $this->paginate_var
        );

        return $this->loadedMessages;
    }

    public function sendMessage(): void
    {
        $this->validate(['body' => 'required|string|min:3']);
        $createdMessage = app(MessagesService::class)->createMessage($this->selectedRoom->id, auth()->id(), $this->selectedRoom->getReceiver()->id, $this->body);
        $this->body = '';
        $this->dispatchBrowserEvent('scroll-bottom');
        $this->loadedMessages->push($createdMessage);
        $this->selectedRoom->updated_at = now();
        $this->selectedRoom->save();
        $this->emitTo('chat.chat-list', 'refresh');
        event(new \App\Events\MessageSent($createdMessage, $this->selectedRoom->getReceiver(), auth()->user(), $this->selectedRoom));
    }

    public function mount(): void
    {
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.chat.chat-box');
    }
}
