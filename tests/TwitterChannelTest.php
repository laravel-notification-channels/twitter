<?php

namespace NotificationChannels\Twitter\Test;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Mockery as m;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterMessage;
use NotificationChannels\Twitter\TwitterStatusUpdate;
use stdClass;

class TwitterChannelTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    protected $twitter;

    protected TwitterChannel $channel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->twitter = m::mock(TwitterOAuth::class, function ($mock) {
            $mock->shouldReceive('setApiVersion')->with('1.1');
            $mock->shouldReceive('setApiVersion')->with('2');
        });
        $this->channel = new TwitterChannel($this->twitter);
    }

    public function test_it_can_send_a_status_update_notification()
    {
        $this->twitter->shouldReceive('post')
            ->once()
            ->with('tweets', ['text' => 'Laravel Notification Channels are awesome!'], true)
            ->andReturn([]);

        $this->twitter->shouldReceive('getLastHttpCode')
            ->once()
            ->andReturn(201);

        $this->channel->send(new TestNotifiable, new TestNotification);
    }

    public function test_it_can_send_a_status_update_notification_with_images()
    {
        $media = new stdClass;
        $media->media_id_string = '2';

        $this->twitter->shouldReceive('setTimeouts')
            ->once()
            ->with(10, 15);

        $this->twitter->shouldReceive('post')
            ->once()
            ->with(
                'tweets',
                ['text' => 'Laravel Notification Channels are awesome!', 'media' => ['media_ids' => [2]]],
                true
            )
            ->andReturn([]);

        $this->twitter->shouldReceive('upload')
            ->once()
            ->with('media/upload', ['media' => public_path('image.png')])
            ->andReturn($media);

        $this->twitter->shouldReceive('getLastHttpCode')
            ->once()
            ->andReturn(201);

        $this->channel->send(new TestNotifiable, new TestNotificationWithImage);
    }

    public function test_it_can_send_a_status_update_notification_with_videos()
    {
        $media = new stdClass;
        $media->media_id_string = '2';

        $status = new stdClass;
        $status->media_id_string = '2';
        $status->processing_info = new stdClass;
        $status->processing_info->state = 'completed';

        $this->twitter->shouldReceive('setTimeouts')
            ->once()
            ->with(10, 15);

        $this->twitter->shouldReceive('post')
            ->once()
            ->with(
                'tweets',
                ['text' => 'Laravel Notification Channels are awesome!', 'media' => ['media_ids' => [2]]],
                ['jsonPayload' => true]
            )
            ->andReturn([]);

        $this->twitter->shouldReceive('upload')
            ->once()
            ->with('media/upload', [
                'media' => public_path('video.mp4'),
                'media_category' => 'tweet_video',
                'media_type' => 'video/mp4',
            ], true)
            ->andReturn($media);

        $this->twitter->shouldReceive('mediaStatus')
            ->once()
            ->with($media->media_id_string)
            ->andReturn($status);

        $this->twitter->shouldReceive('getLastHttpCode')
            ->once()
            ->andReturn(201);

        $this->channel->send(new TestNotifiable, new TestNotificationWithVideo);
    }

    public function test_it_can_send_a_status_update_notification_with_reply_to_tweet_id(): void
    {
        $postParams = [
            'text' => 'Laravel Notification Channels are awesome!',
            'in_reply_to_tweet_id' => $replyToStatusId = 123,
        ];

        $this->twitter->shouldReceive('post')
            ->once()
            ->with('tweets', $postParams, true)
            ->andReturn([]);

        $this->twitter->shouldReceive('getLastHttpCode')
            ->once()
            ->andReturn(201);

        $this->channel->send(new TestNotifiable, new TestNotificationWithReplyToStatusId($replyToStatusId));
    }

    public function test_it_throws_an_exception_when_it_could_not_send_the_notification()
    {
        $twitterResponse = new stdClass;
        $twitterResponse->detail = 'Error Message';

        $this->twitter->shouldReceive('post')
            ->once()
            ->with('tweets', ['text' => 'Laravel Notification Channels are awesome!'], true);

        $this->twitter->shouldReceive('getLastHttpCode')
            ->once()
            ->andReturn(500);

        $this->twitter->shouldReceive('getLastBody')
            ->once()
            ->andReturn($twitterResponse);

        $this->expectException(CouldNotSendNotification::class);

        $this->channel->send(new TestNotifiable, new TestNotification);
    }

    public function test_it_throws_an_exception_when_it_could_not_send_the_notification_with_videos()
    {
        $media = new stdClass;
        $media->media_id_string = '2';

        $status = new stdClass;
        $status->media_id_string = '2';
        $status->processing_info = new stdClass;
        $status->processing_info->state = 'failed';
        $status->processing_info->error = new stdClass;
        $status->processing_info->error->message = 'invalid media';

        $this->twitter->shouldReceive('setTimeouts')
            ->once()
            ->with(10, 15);

        $this->twitter->shouldReceive('upload')
            ->once()
            ->with('media/upload', [
                'media' => public_path('video.mp4'),
                'media_category' => 'tweet_video',
                'media_type' => 'video/mp4',
            ], true)
            ->andReturn($media);

        $this->twitter->shouldReceive('mediaStatus')
            ->once()
            ->with($media->media_id_string)
            ->andReturn($status);

        $this->expectException(CouldNotSendNotification::class);

        $this->channel->send(new TestNotifiable, new TestNotificationWithVideo);
    }
}

class TestNotifiable
{
    use Notifiable;

    /**
     * @return false
     */
    public function routeNotificationForTwitter()
    {
        return false;
    }
}

class TestNotifiableWithDifferentSettings
{
    use Notifiable;

    /**
     * @return array
     */
    public function routeNotificationForTwitter()
    {
        return ['1', '2', '3', '4'];
    }
}

class TestNotification extends Notification
{
    /**
     * @throws CouldNotSendNotification
     */
    public function toTwitter(mixed $notifiable): TwitterMessage
    {
        return new TwitterStatusUpdate('Laravel Notification Channels are awesome!');
    }
}

class TestNotificationWithImage extends Notification
{
    /**
     * @throws CouldNotSendNotification
     */
    public function toTwitter(mixed $notifiable): TwitterMessage
    {
        return (new TwitterStatusUpdate('Laravel Notification Channels are awesome!'))->withImage(public_path('image.png'));
    }
}

class TestNotificationWithVideo extends Notification
{
    /**
     * @throws CouldNotSendNotification
     */
    public function toTwitter(mixed $notifiable): TwitterMessage
    {
        return (new TwitterStatusUpdate('Laravel Notification Channels are awesome!'))->withVideo(public_path('video.mp4'));
    }
}

class TestNotificationWithReplyToStatusId extends Notification
{
    private int $replyToStatusId;

    public function __construct(int $replyToStatusId)
    {
        $this->replyToStatusId = $replyToStatusId;
    }

    public function toTwitter(mixed $notifiable): TwitterMessage
    {
        return (new TwitterStatusUpdate('Laravel Notification Channels are awesome!'))
            ->inReplyTo($this->replyToStatusId);
    }
}
