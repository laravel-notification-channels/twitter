<?php

namespace NotificationChannels\Twitter\Test;

use Abraham\TwitterOAuth\Response;
use Abraham\TwitterOAuth\TwitterOAuth;
use Mockery;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\Twitter;
use NotificationChannels\Twitter\TwitterStatusUpdate;
use Orchestra\Testbench\TestCase;
use stdClass;

class ChannelTest extends TestCase
{
    /** @var Mockery\Mock */
    protected $twitter;

    /** @var \NotificationChannels\Twitter\TwitterChannel */
    protected $channel;

    public function setUp()
    {
        parent::setUp();
        $this->twitter = Mockery::mock(TwitterOAuth::class);
        $this->channel = new TwitterChannel($this->twitter);
    }

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_send_a_status_update_notification()
    {
        $this->twitter->shouldReceive('post')
            ->once()
            ->with('statuses/update', ['status' => 'Laravel Notification Channels are awesome!']);

        $this->twitter->shouldReceive('getLastHttpCode')
            ->once()
            ->andReturn(200);

        $this->channel->send(new TestNotifiable(), new TestNotification());
    }

    /** @test */
    public function it_can_send_a_status_update_notification_with_images()
    {
        $media = new stdClass;
        $media->media_id_string = "2";

        $this->twitter->shouldReceive('setTimeouts')
            ->once()
            ->with(10, 15);

        $this->twitter->shouldReceive('post')
            ->once()
            ->with('statuses/update', ['status' => 'Laravel Notification Channels are awesome!', 'media_ids' => '2']);

        $this->twitter->shouldReceive('upload')
            ->once()
            ->with('media/upload', ['media' => public_path('image.png')])
            ->andReturn($media);

        $this->twitter->shouldReceive('getLastHttpCode')
            ->once()
            ->andReturn(200);

        $this->channel->send(new TestNotifiable(), new TestNotification2());
    }

    /** @test */
    public function it_throws_an_exception_when_it_could_not_send_the_notification()
    {
        $messageObject = new stdClass;
        $messageObject->message = 'Error message';
        $twitterResponse = new stdClass;
        $twitterResponse->errors[] = $messageObject;


        $this->twitter->shouldReceive('post')
            ->once()
            ->with('statuses/update', ['status' => 'Laravel Notification Channels are awesome!']);

        $this->twitter->shouldReceive('getLastHttpCode')
        ->once()
        ->andReturn(500);

        $this->twitter->shouldReceive('getLastBody')
            ->once()
            ->andReturn($twitterResponse);

        $this->setExpectedException(CouldNotSendNotification::class);

        $this->channel->send(new TestNotifiable(), new TestNotification());
    }
}


class TestNotifiable
{
    use \Illuminate\Notifications\Notifiable;

    /**
     * @return int
     */
    public function routeNotificationForTwitter()
    {
        return 'FooBar';
    }
}
class TestNotification extends Notification
{
    public function toTwitter($notifiable)
    {
        return new TwitterStatusUpdate('Laravel Notification Channels are awesome!');
    }
}

class TestNotification2 extends Notification
{
    public function toTwitter($notifiable)
    {
        return new TwitterStatusUpdate('Laravel Notification Channels are awesome!', [public_path('image.png')]);
    }
}
