<?php

namespace NotificationChannels\Twitter;

class TwitterImage
{
    public function __construct(private string $imagePath)
    {
        //
    }

    public function getPath(): string
    {
        return $this->imagePath;
    }
}
