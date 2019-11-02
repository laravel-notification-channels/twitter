<?php

namespace NotificationChannels\Twitter;

use Illuminate\Support\Collection;
use Kylewm\Brevity\Brevity;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;

class TwitterStatusUpdate
{
    /** @var string */
    protected $content;

    /** @var array */
    private $images;

    /** @var bool */
    public $isJsonRequest = false;

    /** @var Collection */
    public $imageIds;

    /** @var string */
    private $apiEndpoint = 'statuses/update';

    /**
     * TwitterStatusUpdate constructor.
     *
     * @param $content
     * @throws CouldNotSendNotification
     */
    public function __construct($content)
    {
        if ($exceededLength = $this->messageIsTooLong($content, new Brevity())) {
            throw CouldNotSendNotification::statusUpdateTooLong($exceededLength);
        }

        $this->content = $content;
    }

    /**
     * Set Twitter media files.
     *
     * @param   array|string $images
     * @return  $this
     */
    public function withImage($images)
    {
        collect($images)->each(function ($image) {
            $this->images[] = new TwitterImage($image);
        });

        return $this;
    }

    /**
     * Get Twitter status update content.
     *
     * @return  string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get Twitter images list.
     *
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Return Twitter status update api endpoint.
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
     * @return  array
     */
    public function getRequestBody()
    {
        $body = [
            'status' => $this->getContent(),
        ];

        if ($this->imageIds) {
            $body['media_ids'] = $this->imageIds->implode(',');
        }

        return $body;
    }

    /**
     * Check if the message length is too long.
     *
     * @param $content
     * @param $brevity
     * @return int
     */
    private function messageIsTooLong($content, Brevity $brevity)
    {
        $tweetLength = $brevity->tweetLength($content);
        $exceededLength = $tweetLength - 280;

        return $exceededLength > 0 ? $exceededLength : 0;
    }
}
