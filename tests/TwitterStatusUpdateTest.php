<?php

namespace NotificationChannels\Twitter\Test;

use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;
use NotificationChannels\Twitter\TwitterImage;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class TwitterStatusUpdateTest extends TestCase
{
    protected TwitterStatusUpdate $message;

    /** @test */
    public function it_accepts_a_message_when_constructing_a_message(): void
    {
        $message = new TwitterStatusUpdate('myMessage');

        $this->assertEquals('myMessage', $message->getContent());
    }

    /** @test */
    public function image_paths_parameter_is_optional(): void
    {
        $message = new TwitterStatusUpdate('myMessage');

        $this->assertEquals(null, $message->getImages());
    }

    /** @test */
    public function it_accepts_one_image_path(): void
    {
        $message = (new TwitterStatusUpdate('myMessage'))->withImage('image1.png');

        $this->assertEquals('myMessage', $message->getContent());
        $this->assertEquals([new TwitterImage('image1.png')], $message->getImages());
    }

    /** @test */
    public function it_accepts_array_of_image_paths(): void
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
    public function it_constructs_a_request_body(): void
    {
        $message = new TwitterStatusUpdate('myMessage');
        $message->imageIds = collect([434, 435, 436]);

        $this->assertEquals($message->getRequestBody(), [
            'status'    => 'myMessage',
            'media_ids' => '434,435,436',
        ]);
    }

    /** @test */
    public function it_throws_an_exception_when_the_status_update_is_too_long(): void
    {
        $tooLongMessage = 'This is a super intensive long new Twitter status message which includes some super useful and concrete information about an upcoming package test that will check if a certain Twitter message may be too long in the case that the character count is higher than specific prior defined count.';

        try {
            $statusUpdate = new TwitterStatusUpdate($tooLongMessage);
        } catch (CouldNotSendNotification $e) {
            $this->assertEquals(CouldNotSendNotification::class, get_class($e));
        }
    }

    /** @test */
    public function it_provides_exceeded_message_count_when_the_status_update_is_too_long(): void
    {
        $tooLongMessage = 'This is a super intensive long new Twitter status message which includes some super useful and concrete information about an upcoming package test that will check if a certain Twitter message may be too long in the case that the character count is higher than specific prior define count.';

        try {
            $statusUpdate = new TwitterStatusUpdate($tooLongMessage);
        } catch (CouldNotSendNotification $e) {
            $this->assertEquals("Couldn't post notification, because the status message was too long by 8 character(s).",
                $e->getMessage());
        }

        $anotherTooLongMessage = 'This is a super intensive long new Twitter status message which includes some super useful and concrete information about an upcoming package test that will check if a certain Twitter message may be too long in the case that the character count is higher than specific prior define count!!';

        try {
            $statusUpdate = new TwitterStatusUpdate($anotherTooLongMessage);
        } catch (CouldNotSendNotification $e) {
            $this->assertEquals("Couldn't post notification, because the status message was too long by 9 character(s).",
                $e->getMessage());
        }
    }
}
