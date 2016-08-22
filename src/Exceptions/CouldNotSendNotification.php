<?php

namespace NotificationChannels\Twitter\Exceptions;

class CouldNotSendNotification extends \Exception
{
    public static function serviceRespondedWithAnError($response)
    {
        return new static("Couldn't post Twitter status. Error: {$response->errors[0]->code} Message: {$response->errors[0]->message}");
    }

}
