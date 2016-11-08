<?php

namespace NotificationChannels\TWitter\Test;

use NotificationChannels\Twitter\TwitterImage;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class TwitterStatusUpdateTest extends \PHPUnit_Framework_TestCase
{
    /** @var TwitterStatusUpdate */
    protected $message;

    /** @test */
    public function it_accepts_a_message_when_constructing_a_message()
    {
        $message = new TwitterStatusUpdate('myMessage');

        $this->assertEquals('myMessage', $message->getContent());
    }

    /** @test */
    public function image_paths_parameter_is_optional()
    {
        $message = new TwitterStatusUpdate('myMessage');

        $this->assertEquals(null, $message->getImages());
    }

    /** @test */
    public function it_accepts_one_image_path()
    {
        $message = (new TwitterStatusUpdate('myMessage'))->withImage('image1.png');

        $this->assertEquals('myMessage', $message->getContent());
        $this->assertEquals([ new TwitterImage('image1.png') ], $message->getImages());
    }

    /** @test */
    public function it_accepts_array_of_image_paths()
    {
        $imagePaths = ['path1', 'path2'];
        $message = (new TwitterStatusUpdate('myMessage'))->withImage($imagePaths);
        $imagePathsObjects = collect($imagePaths)->map(function($image){ return new TwitterImage($image); })->toArray();

        $this->assertEquals('myMessage', $message->getContent());
        $this->assertEquals($imagePathsObjects, $message->getImages());
    }

    /** @test */
    public function it_constructs_a_request_body()
    {
        $message = new TwitterStatusUpdate('myMessage', ['path1', 'path2', 'path3']);
        $message->imageIds = collect([434, 435, 436]);

        $this->assertEquals($message->getRequestBody(), [
            'status'    => 'myMessage',
            'media_ids' => '434,435,436',
        ]);
    }
}
