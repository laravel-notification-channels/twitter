<?php

namespace NotificationChannels\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;

class TwitterChannel
{
    public function __construct(protected TwitterOAuth $twitter)
    {
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable  Should be an object that uses the Illuminate\Notifications\Notifiable trait.
     *
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, TwitterNotification $notification): array|object
    {
        $this->changeTwitterSettingsIfNeeded($notifiable);

        $twitterMessage = $notification->toTwitter($notifiable);
        $twitterMessage = $this->addImagesIfGiven($twitterMessage);

        $twitterApiResponse = $this->twitter->post(
            $twitterMessage->getApiEndpoint(),
            $twitterMessage->getRequestBody(),
            $twitterMessage->isJsonRequest,
        );

        if ($this->twitter->getLastHttpCode() !== 200) {
            throw CouldNotSendNotification::serviceRespondsNotSuccessful($this->twitter->getLastBody());
        }

        return $twitterApiResponse;
    }

    /**
     * Use per user settings instead of default ones.
     *
     * @param  object  $notifiable  Provide an object that uses the Illuminate\Notifications\Notifiable trait.
     */
    private function changeTwitterSettingsIfNeeded(object $notifiable)
    {
        if (
            method_exists($notifiable, 'routeNotificationFor') &&
            $twitterSettings = $notifiable->routeNotificationFor('twitter')
        ) {
            $this->twitter = new TwitterOAuth(
                $twitterSettings[0],
                $twitterSettings[1],
                $twitterSettings[2],
                $twitterSettings[3],
            );
        }
    }

    /**
     * If it is a status update message and images are provided, add them.
     */
    private function addImagesIfGiven(TwitterMessage $twitterMessage): object
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
