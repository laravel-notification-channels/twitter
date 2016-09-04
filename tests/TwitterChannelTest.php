<?php

namespace NotificationChannels\Twitter\Test;

use Abraham\TwitterOAuth\Response;
use Abraham\TwitterOAuth\TwitterOAuth;
use Mockery;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\Twitter;
use NotificationChannels\Twitter\TwitterMessage;
use NotificationChannels\Twitter\TwitterStatusUpdate;
use Orchestra\Testbench\TestCase;

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
        $response = new Response();
        $response->setHttpCode(200);

        $this->twitter->shouldReceive('post')
            ->once()
            ->with('statuses/update', ['status' => 'Laravel Notification Channels are awesome!'])
            ->andReturn($response);

        $this->channel->send(new TestNotifiable(), new TestNotification());
    }

    /** @test */
    public function it_can_send_a_status_update_notification_with_images()
    {
//        $response = new Response();
//        $response->setHttpCode(200);
//
//        $this->twitter->shouldReceive('setTimeouts')
//            ->once()
//            ->with(10, 15);
//
//        $this->twitter->shouldReceive('post')
//            ->once()
//            ->with('statuses/update', ['status' => 'Laravel Notification Channels are awesome!', 'media_ids' => '1,2,3'])
//            ->andReturn($response);
//
//        $this->channel->send(new TestNotifiable(), new TestNotification());
    }

    /** @test */
    public function it_throws_an_exception_when_it_could_not_send_the_notification()
    {
        $response = new Response();
        $response->setHttpCode(500);

        $this->twitter->shouldReceive('post')
            ->once()
            ->with('statuses/update', ['status' => 'Laravel Notification Channels are awesome!'])
            ->andReturn($response);

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
