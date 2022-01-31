<?php

namespace NotificationChannels\Twitter;

abstract class TwitterMessage
{
    public bool $isJsonRequest = false;

    public function __construct(protected string $content)
    {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    abstract public function getApiEndpoint(): string;
}
