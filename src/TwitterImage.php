<?php

namespace NotificationChannels\Twitter;

class TwitterImage
{
    /**
     * TwitterImage constructor.
     *
     * @param $imagePath
     */
    public function __construct(private string $imagePath)
    {
        //
    }

    /**
     * Get image path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->imagePath;
    }
}
