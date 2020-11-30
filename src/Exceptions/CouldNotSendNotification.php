<?php

namespace NotificationChannels\Twitter\Exceptions;

class CouldNotSendNotification extends \Exception
{
    /**
     * @param $response
     * @return CouldNotSendNotification
     */
    public static function serviceRespondsNotSuccessful($response)
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
    public static function userWasNotFound($response)
    {
        $responseBody = print_r($response->errors[0]->message, true);

        return new static("Couldn't send direct message notification. Response: ".$responseBody);
    }

    /**
     * @param $exceededLength
     * @return CouldNotSendNotification
     */
    public static function statusUpdateTooLong($exceededLength)
    {
        return new static("Couldn't post notification, because the status message was too long by ".$exceededLength.' character(s).');
    }
}
