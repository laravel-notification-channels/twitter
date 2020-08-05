<?php

namespace NotificationChannels\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;

class TwitterChannel
{
    /** @var TwitterOAuth */
    protected $twitter;

    /** @param TwitterOAuth $twitter */
    public function __construct(TwitterOAuth $twitter)
    {
        $this->twitter = $twitter;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $this->changeTwitterSettingsIfNeeded($notifiable);

        $twitterMessage = $notification->toTwitter($notifiable);
        $twitterMessage = $this->addImagesIfGiven($twitterMessage);

        $twitterApiResponse = $this->twitter->post($twitterMessage->getApiEndpoint(), $twitterMessage->getRequestBody($this->twitter),
            $twitterMessage->isJsonRequest);

        if ($this->twitter->getLastHttpCode() !== 200) {
            throw CouldNotSendNotification::serviceRespondsNotSuccessful($this->twitter->getLastBody());
        }

        return $twitterApiResponse;
    }

    /**
     * Use per user settings instead of default ones.
     *
     * @param Notifiable $notifiable
     */
    private function changeTwitterSettingsIfNeeded($notifiable)
    {
        if ($twitterSettings = $notifiable->routeNotificationFor('twitter')) {
            $this->twitter = new TwitterOAuth($twitterSettings[0], $twitterSettings[1], $twitterSettings[2],
                $twitterSettings[3]);
        }
    }

    /**
     * If it is a status update message and images are provided, add them.
     *
     * @param $twitterMessage
     * @return mixed
     */
    private function addImagesIfGiven($twitterMessage)
    {
        if (is_a($twitterMessage, TwitterStatusUpdate::class) && $twitterMessage->getImages()) {
            $this->twitter->setTimeouts(10, 15);

            $twitterMessage->imageIds = collect($twitterMessage->getImages())->map(function (TwitterImage $image) {
                $media = $this->twitter->upload('media/upload', ['media' => $image->getPath()]);

                return $media->media_id_string;
            });
        }

        return $twitterMessage;
    }
}
