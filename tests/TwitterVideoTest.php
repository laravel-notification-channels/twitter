<?php

namespace NotificationChannels\Twitter;

function mime_content_type($path)
{
    return 'video/mp4';
}

namespace NotificationChannels\Twitter\Test;

use NotificationChannels\Twitter\TwitterVideo;

class TwitterVideoTest extends TestCase
{
    /** @test */
    public function it_accepts_a_video_path_when_constructing_a_twitter_video(): void
    {
        $video = new TwitterVideo('/foo/bar/baz.mp4');
        $this->assertEquals('/foo/bar/baz.mp4', $video->getPath());
        $this->assertEquals("video/mp4", $video->getMimeType());
    }
}
