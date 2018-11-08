<?php

namespace NotificationChannels\Twitter;

class TwitterImage
{
    /** @var string */
    private $imagePath;

    /**
     * TwitterImage constructor.
     *
     * @param $imagePath
     */
    public function __construct($imagePath)
    {
        $this->imagePath = $imagePath;
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
