<?php

namespace NotificationChannels\Twitter\Exceptions;

class CouldNotSendNotification extends \Exception
{
    /**
     * @param $response
     * @return static
     */
    public static function serviceRespondsNotSuccessful($response): static
    {
        if (isset($response->error)) {
            return new static("Couldn't post notification. Response: ".$response->error);
        }

        $responseBody = print_r($response->errors[0]->message, true);

        return new static("Couldn't post notification. Response: ".$responseBody);
    }

    /**
     * @param $response
     * @return static
     */
    public static function userWasNotFound($response): static
    {
        $responseBody = print_r($response->errors[0]->message, true);

        return new static("Couldn't send direct message notification. Response: ".$responseBody);
    }

    /**
     * @param $exceededLength
     * @return static
     */
    public static function statusUpdateTooLong($exceededLength): static
    {
        return new static("Couldn't post notification, because the status message was too long by ".$exceededLength.' character(s).');
    }
}
