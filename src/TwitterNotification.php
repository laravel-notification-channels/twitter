<?php

namespace NotificationChannels\Twitter;

use Illuminate\Notifications\Notification;

abstract class TwitterNotification extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TwitterChannel::class];
    }

    /**
     * @param  mixed  $notifiable  Should be an object that uses the Illuminate\Notifications\Notifiable trait.
     * @return TwitterMessage
     */
    abstract public function toTwitter(mixed $notifiable): TwitterMessage;
}
