<?php

namespace NotificationChannels\Twitter;

class TwitterDirectMessage
{
    /** @var string */
    private $content;

    /**
     * @var  string
     */
    private $to;

    /**
     * @var  string
     */
    private $apiEndpoint = 'direct_messages/new';

    /*
     * @param  string $content
     */
    public function __construct($to, $content)
    {
        $this->to = $to;
        $this->content = $content;
    }

    /**
     * Get Twitter direct messsage content.
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
     * @return  string
     */
    public function getReceiver()
    {
        return $this->to;
    }

    /**
     * Return Twitter direct message api endpoint.
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
            'screen_name' => $this->getReceiver(),
            'text'        => $this->getContent(),
        ];

        return $body;
    }
}
