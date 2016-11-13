<?php

namespace NotificationChannels\Twitter\Exceptions;

class CouldNotSendNotification extends \Exception
{
    public static function serviceRespondedWithAnError($response)
    {
        $responseBody = print_r($response->errors[0]->message, true);

        return new static("Couldn't post Notification. Response: ".$responseBody);
    }

    public static function statusUpdateTooLong($exceededLength)
    {
        return new static("Couldn't post Notification, because the status message was too long by " .
            $exceededLength . " character(s).");
    }
}
