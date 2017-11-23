<?php

namespace NotificationChannels\Twitter\Test;

use NotificationChannels\Twitter\TwitterImage;
use NotificationChannels\Twitter\TwitterStatusUpdate;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;

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
        $this->assertEquals([new TwitterImage('image1.png')], $message->getImages());
    }

    /** @test */
    public function it_accepts_array_of_image_paths()
    {
        $imagePaths = ['path1', 'path2'];
        $message = (new TwitterStatusUpdate('myMessage'))->withImage($imagePaths);
        $imagePathsObjects = collect($imagePaths)->map(function ($image) {
            return new TwitterImage($image);
        })->toArray();

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

    /** @test */
    public function it_throws_an_exception_when_the_status_update_is_too_long()
    {
        $tooLongMessage = 'Laravel Notification Channels are awesome and this message is far too long for a Twitter 
            status update and this is why an exception is thrown!';

        try {
            $statusUpdate = new TwitterStatusUpdate($tooLongMessage);
        } catch (CouldNotSendNotification $e) {
            $this->assertEquals(CouldNotSendNotification::class, get_class($e));
        }
    }

    /** @test */
    public function it_provides_exceeded_message_count_when_the_status_update_is_too_long()
    {
        $tooLongMessage = 'Laravel Notification Channels are awesome and this message is far too long for a Twitter status update because of this URL https://github.com/laravel-notification-channels';

        try {
            $statusUpdate = new TwitterStatusUpdate($tooLongMessage);
        } catch (CouldNotSendNotification $e) {
            $this->assertEquals("Couldn't post Notification, because the status message was too long by 6 character(s).",
                $e->getMessage());
        }

        $anotherTooLongMessage = 'Laravel Notification Channels are awesome and this message is just in length so that Twitter does not complain!!!!!!! https://github.com/laravel-notification-channels';

        try {
            $statusUpdate = new TwitterStatusUpdate($anotherTooLongMessage);
        } catch (CouldNotSendNotification $e) {
            $this->assertEquals("Couldn't post Notification, because the status message was too long by 1 character(s).",
                $e->getMessage());
        }
    }
}
