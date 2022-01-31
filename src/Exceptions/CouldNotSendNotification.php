<?php

namespace NotificationChannels\Twitter\Exceptions;

use Exception;

class CouldNotSendNotification extends Exception
{
    /**
     * @param $response
     * @return CouldNotSendNotification
     */
    public static function serviceRespondsNotSuccessful($response): CouldNotSendNotification
    {
        if (isset($response->error)) {
            return new static("Couldn't post notification. Response: ".$response->error);
        }

        $responseBody = print_r($response->errors[0]->message, true);

        return new static("Couldn't post notification. Response: ".$responseBody);
    }

    /**
     * @param $response
     * @return CouldNotSendNotification
     */
    public static function userWasNotFound($response): CouldNotSendNotification
    {
        $responseBody = print_r($response->errors[0]->message, true);

        return new static("Couldn't send direct message notification. Response: " . $responseBody);
    }

    public static function statusUpdateTooLong(int $exceededLength): CouldNotSendNotification
    {
        return new static(
            "Couldn't post notification, because the status message was too long by ${exceededLength} character(s)."
        );
    }
}
