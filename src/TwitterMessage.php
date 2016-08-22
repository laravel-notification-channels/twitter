<?php

namespace NotificationChannels\Twitter;

class TwitterMessage
{
    /** @var string */
    protected $content;

    /**
     * @param string $content
     *
     * @return static
     */
    public static function create($content = '')
    {
        return new static($content);
    }

    /*
     * @param string $content
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * Get Twitter message content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
