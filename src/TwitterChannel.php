<?php
namespace NotificationChannels\Twitter;

use GuzzleHttp\Client;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\Exceptions\InvalidConfiguration;

class TwitterChannel
{

    /** @var Client */
    protected $client;
    /**
     * @var Twitter
     */
    private $twitter;

    /**
     * @param Client $client
     * @param Twitter $twitter
     */
    public function __construct(Client $client, Twitter $twitter)
    {
        $this->client = $client;
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
        $twitterMessage = $notification->toTwitter($notifiable);

        $response = $this->twitter->connection->post(
            "statuses/update", ["status" => $twitterMessage->getContent()]);

        if (isset($response->errors)) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($response);
        }

    }

}

