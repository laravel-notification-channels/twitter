<?php

namespace NotificationChannels\Twitter;

class TwitterStatusUpdate
{

    /** @var  string */
    protected $content;

    /**
     * @var  array
     */
    private $imagePaths;

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
    public function __construct(string $content, array $imagePaths = null)
    {
        $this->content = $content;
        $this->imagePaths = $imagePaths ? collect($imagePaths) : null;
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
     * Get Twitter status update image paths.
     *
     * @return  string
     */
    public function getImagePaths()
    {
        return $this->imagePaths;
    }

    /**
     * Return Twitter status update api endpoint
     * @return  string
     */
    public function getApiEndpoint() {
        return $this->apiEndpoint;
    }

    /**
     * Build Twitter request body
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
