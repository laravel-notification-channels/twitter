<?php

namespace NotificationChannels\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;

class TwitterDirectMessage
{
    /** @var string */
    private $content;

    /** @var string */
    private $to;

    /** @var bool */
    public $isJsonRequest = true;

    /** @var string */
    private $apiEndpoint = 'direct_messages/events/new';

    /**
     * TwitterDirectMessage constructor.
     *
     * @param $to
     * @param $content
     */
    public function __construct($to, $content)
    {
        $this->to = $to;
        $this->content = $content;
    }

    /**
     * Get Twitter direct message content.
     *
     * @return  string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get Twitter direct message receiver.
     *
     * @param TwitterOAuth $twitter
     * @return  string
     * @throws CouldNotSendNotification
     */
    public function getReceiver(TwitterOAuth $twitter)
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
     * Return Twitter direct message api endpoint.
     *
     * @return  string
     */
    public function getApiEndpoint()
    {
        return $this->apiEndpoint;
    }

    /**
     * Build Twitter request body.
     *
     * @param TwitterOAuth $twitter
     * @return  array
     * @throws CouldNotSendNotification
     */
    public function getRequestBody(TwitterOAuth $twitter)
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
