<?php

namespace NotificationChannels\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;

class TwitterChannel
{
    /** @var TwitterOAuth */
    protected $twitter;

    /**
     * @param TwitterOAuth $twitter
     */
    public function __construct(TwitterOAuth $twitter)
    {
        $this->twitter = $twitter;
    }

    /**
     * Send the given notification.
     *
     * @param mixed                                  $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $twitterMessage = $notification->toTwitter($notifiable);
        if (is_a($twitterMessage, TwitterStatusUpdate::class) && $twitterMessage->getImagePaths()) {
            $this->twitter->setTimeouts(10, 15);

            $twitterMessage->imageIds = $twitterMessage->getImagePaths()->map(function ($path) {
                $media = $this->twitter->upload('media/upload', ['media' => $path]);

                return $media->media_id_string;
            });
        }

        $response = $this->twitter->post($twitterMessage->getApiEndpoint(), $twitterMessage->getRequestBody());

        if ($response->getHttpCode() !== 200) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($response);
        }
    }
}
