<?php

namespace NotificationChannels\Twitter;

class TwitterMessage
{
    /** @var string */
    protected $content;
    protected $to;

    /**
     * @param string $content
     *
     * @return static
     */
    public static function create()
    {
        $args = func_get_args();
        if (count($args) == 2) {
            return new static($args[0], $args[1]);
        } else {
            return new static($args[0]);
        }
    }

    /*
     * @param string $content
     */
    public function __construct()
    {
        $args = func_get_args();
        if (count($args) == 2) {
            $this->to = $args[0];
            $this->content = $args[1];
        } else {
            $this->content = $args[0];
        }
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

    /**
     * Get Twitter message receiver.
     *
     * @return string
     */
    public function getReceiver()
    {
        return $this->to;
    }
}
