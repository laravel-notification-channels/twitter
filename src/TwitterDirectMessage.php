<?php

namespace NotificationChannels\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;

class TwitterDirectMessage extends TwitterMessage
{
    public bool $isJsonRequest = true;

    public function __construct(private string|int $to, string $content)
    {
        parent::__construct($content);
    }

    public function getApiEndpoint(): string
    {
        return 'direct_messages/events/new';
    }

    /**
     * Get Twitter direct message receiver.
     *
     * @param  TwitterOAuth  $twitter
     * @return string|mixed
     *
     * @throws CouldNotSendNotification
     */
    public function getReceiver(TwitterOAuth $twitter): mixed
    {
        if (is_int($this->to)) {
            return $this->to;
        }

        $user = $twitter->get('users/show', [
            'screen_name' => $this->to,
            'include_user_entities' => false,
            'skip_status' => true,
        ]);

        if ($twitter->getLastHttpCode() === 404) {
            throw CouldNotSendNotification::userWasNotFound($twitter->getLastBody());
        }

        return $user->id;
    }

    /**
     * Build Twitter request body.
     *
     * @param  TwitterOAuth  $twitter
     * @return array
     *
     * @throws CouldNotSendNotification
     */
    public function getRequestBody(TwitterOAuth $twitter): array
    {
        return [
            'event' => [
                'type' => 'message_create',
                'message_create' => [
                    'target' => [
                        'recipient_id' => $this->getReceiver($twitter),
                    ],
                    'message_data' => [
                        'text' => $this->getContent(),
                    ],
                ],
            ],
        ];
    }
}
