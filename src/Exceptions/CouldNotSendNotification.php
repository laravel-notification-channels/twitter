<?php

namespace NotificationChannels\Twitter\Exceptions;

class CouldNotSendNotification extends \Exception
{
    public static function serviceRespondedWithAnError($response)
    {
        $responseBody = print_r($response->getBody(), true);
        return new static("Couldn't post Twitter status. Response: ".$responseBody);
    }
}
