<?php

namespace NotificationChannels\Twitter\Test;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Notifications\Notification;
use Mockery;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterMessage;
use NotificationChannels\Twitter\TwitterNotification;
use NotificationChannels\Twitter\TwitterStatusUpdate;
use stdClass;

class TwitterChannelTest extends TestCase
{
    /** @var Mockery\Mock */
    protected $twitter;

    /** @var \NotificationChannels\Twitter\TwitterChannel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->twitter = Mockery::mock(TwitterOAuth::class);
        $this->channel = new TwitterChannel($this->twitter);
    }

    /** @test */
    public function it_can_send_a_status_update_notification()
    {
        $this->twitter->shouldReceive('post')
            ->once()
            ->with('statuses/update', ['status' => 'Laravel Notification Channels are awesome!'], false)
            ->andReturn([]);

        $this->twitter->shouldReceive('getLastHttpCode')
            ->once()
            ->andReturn(200);

        $this->channel->send(new TestNotifiable(), new TestNotification());
    }

    /** @test */
    public function it_can_send_a_status_update_notification_with_images()
    {
        $media = new stdClass;
        $media->media_id_string = '2';

        $this->twitter->shouldReceive('setTimeouts')
            ->once()
            ->with(10, 15);

        $this->twitter->shouldReceive('post')
            ->once()
            ->with(
                'statuses/update',
                ['status' => 'Laravel Notification Channels are awesome!', 'media_ids' => '2'],
                false
            )
            ->andReturn([]);

        $this->twitter->shouldReceive('upload')
            ->once()
            ->with('media/upload', ['media' => public_path('image.png')])
            ->andReturn($media);

        $this->twitter->shouldReceive('getLastHttpCode')
            ->once()
            ->andReturn(200);

        $this->channel->send(new TestNotifiable(), new TestNotificationWithImage());
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
            ->with('statuses/update', ['status' => 'Laravel Notification Channels are awesome!'], false);

        $this->twitter->shouldReceive('getLastHttpCode')
            ->once()
            ->andReturn(500);

        $this->twitter->shouldReceive('getLastBody')
            ->once()
            ->andReturn($twitterResponse);

        $this->expectException(CouldNotSendNotification::class);

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
        return false;
    }
}

class TestNotifiableWithDifferentSettings
{
    use \Illuminate\Notifications\Notifiable;

    /** @return array */
    public function routeNotificationForTwitter()
    {
        return ['1', '2', '3', '4'];
    }
}

class TestNotification extends TwitterNotification
{
    /**
     * @param  mixed  $notifiable
     * @return TwitterStatusUpdate
     *
     * @throws CouldNotSendNotification
     */
    public function toTwitter(mixed $notifiable): TwitterMessage
    {
        return new TwitterStatusUpdate('Laravel Notification Channels are awesome!');
    }
}

class TestNotificationWithImage extends TwitterNotification
{
    /**
     * @param  mixed  $notifiable
     * @return TwitterStatusUpdate
     *
     * @throws CouldNotSendNotification
     */
    public function toTwitter(mixed $notifiable): TwitterMessage
    {
        return (new TwitterStatusUpdate('Laravel Notification Channels are awesome!'))->withImage(public_path('image.png'));
    }
}
