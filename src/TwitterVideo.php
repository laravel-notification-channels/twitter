<?php

namespace NotificationChannels\Twitter;

class TwitterVideo
{
    public function __construct(private string $videoPath)
    {
    }

    public function getPath(): string
    {
        return $this->videoPath;
    }

    public function getMimeType(): string
    {
        return mime_content_type($this->videoPath);
    }
}
