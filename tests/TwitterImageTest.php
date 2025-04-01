<?php

namespace NotificationChannels\Twitter\Test;

use NotificationChannels\Twitter\TwitterImage;

class TwitterImageTest extends TestCase
{
    public function test_it_accepts_an_image_path_when_constructing_a_twitter_image(): void
    {
        $image = new TwitterImage('/foo/bar/baz.png');
        $this->assertEquals('/foo/bar/baz.png', $image->getPath());
    }
}
