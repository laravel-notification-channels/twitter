<?php

namespace NotificationChannels\Twitter;

class TwitterMessage
{

    /** @var string @var string */
    protected $content;

    /**
     * Message constructor.
     *
     * @param string $content
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * Get Twitter message content
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

}
