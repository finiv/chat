<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use \App\Events\MessageSent;
use \App\Notifications\MessageSent as Notification;
class MessageSentListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  MessageSent  $event
     * @return void
     */
    public function handle(MessageSent $event)
    {
        $event->userToNotify->notify(new Notification(
            $event->authUser,
            $event->createdMessage,
            $event->room,
            $event->userToNotify->id
        ));
    }
}
