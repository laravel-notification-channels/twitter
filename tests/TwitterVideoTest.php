<?php

namespace NotificationChannels\Twitter\Test;

use NotificationChannels\Twitter\TwitterVideo;

class TwitterVideoTest extends TestCase
{
    public function test_it_accepts_a_video_path_when_constructing_a_twitter_video(): void
    {
        $video = new TwitterVideo('/foo/bar/baz.mp4');
        $this->assertEquals('/foo/bar/baz.mp4', $video->getPath());
        $this->assertEquals('video/mp4', $video->getMimeType());
    }
}
