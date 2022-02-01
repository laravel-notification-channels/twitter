<?php

namespace NotificationChannels\Twitter\Test;

use Abraham\TwitterOAuth\TwitterOAuth;
use Mockery as m;
use NotificationChannels\Twitter\TwitterDirectMessage;

class TwitterDirectMessageTest extends TestCase
{
    protected TwitterDirectMessage $messageWithUserId;
    protected TwitterDirectMessage $messageWithScreenName;
    protected TwitterOAuth $twitter;

    public function setUp(): void
    {
        parent::setUp();
        $this->twitter = m::mock(TwitterOAuth::class);
        $this->messageWithUserId = new TwitterDirectMessage(1234, 'myMessage');
        $this->messageWithScreenName = new TwitterDirectMessage('receiver', 'myMessage');
    }

    /** @test */
    public function it_accepts_receiver_and_message_when_constructed(): void
    {
        $this->assertEquals(1234, $this->messageWithUserId->getReceiver($this->twitter));
        $this->assertEquals('myMessage', $this->messageWithUserId->getContent());
    }

    /** @test */
    public function it_can_get_the_content(): void
    {
        $this->assertEquals('myMessage', $this->messageWithUserId->getContent());
    }

    /** @test */
    public function it_can_get_the_receiver(): void
    {
        $this->assertEquals(1234, $this->messageWithUserId->getReceiver($this->twitter));
    }

    /** @test */
    public function it_can_get_the_receiver_for_screen_name(): void
    {
        $this->twitter->shouldReceive('get')
            ->once()
            ->with('users/show', [
                'screen_name' => 'receiver',
                'include_user_entities' => false,
                'skip_status' => true,
            ])->once()->andReturn((object) ['id' => 1234]);

        $this->twitter->shouldReceive('getLastHttpCode')
            ->once()
            ->andReturn(200);

        $this->assertEquals(1234, $this->messageWithScreenName->getReceiver($this->twitter));
    }

    /** @test */
    public function it_can_get_the_api_endpoint(): void
    {
        $this->assertEquals('direct_messages/events/new', $this->messageWithUserId->getApiEndpoint());
    }

    /** @test */
    public function it_can_get_the_request_body(): void
    {
        $expected = [
            'event' => [
                'type' => 'message_create',
                'message_create' => [
                    'target' => [
                        'recipient_id' => 1234,
                    ],
                    'message_data' => [
                        'text' => 'myMessage',
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->messageWithUserId->getRequestBody($this->twitter));
    }
}
