<?php

namespace NotificationChannels\Twitter\Contracts;

use NotificationChannels\Twitter\TwitterMessage;

interface TwitterNotification
{
    /**
     * @param  mixed  $notifiable  Should be an object that uses the Illuminate\Notifications\Notifiable trait.
     * @return TwitterMessage
     */
    public function toTwitter(mixed $notifiable): TwitterMessage;
}
