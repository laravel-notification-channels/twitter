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
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        if ($twitterSettings = $notifiable->routeNotificationFor('twitter')) {
            $this->switchSettings($twitterSettings);
        }

        $twitterMessage = $notification->toTwitter($notifiable);
        if (is_a($twitterMessage, TwitterStatusUpdate::class) && $twitterMessage->getImages()) {
            $this->twitter->setTimeouts(10, 15);

            $twitterMessage->imageIds = collect($twitterMessage->getImages())->map(function ($image) {
                $media = $this->twitter->upload('media/upload', ['media' => $image->getPath()]);

                return $media->media_id_string;
            });
        }

        $this->twitter->post($twitterMessage->getApiEndpoint(), $twitterMessage->getRequestBody());

        if ($this->twitter->getLastHttpCode() !== 200) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($this->twitter->getLastBody());
        }
    }

    /**
     * Use per user settings instead of default ones
     * @param $twitterSettings
     */
    private function switchSettings($twitterSettings)
    {
        $this->twitter = new TwitterOAuth($twitterSettings[0], $twitterSettings[1], $twitterSettings[2],
            $twitterSettings[3]);
    }

}
