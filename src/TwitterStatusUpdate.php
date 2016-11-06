<?php

namespace NotificationChannels\Twitter;

use NotificationChannels\Twitter\TwitterImage;

class TwitterStatusUpdate
{
    /** @var string */
    protected $content;

    /**
     * @var  array
     */
    private $images;

    /**
     * @var  array
     */
    public $imageIds;

    /**
     * @var  string
     */
    private $apiEndpoint = 'statuses/update';

    /*
     * @param  string $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Set Twitter media files.
     *
     * @return  $this
     */
    public function withImage($images){
        collect($images)->each(function($image){
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
     * @return  string
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Return Twitter status update api endpoint.
     * @return  string
     */
    public function getApiEndpoint()
    {
        return $this->apiEndpoint;
    }

    /**
     * Build Twitter request body.
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
}
