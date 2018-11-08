<?php

namespace NotificationChannels\Twitter\Test;

use Abraham\TwitterOAuth\TwitterOAuth;
use Mockery;
use NotificationChannels\Twitter\TwitterDirectMessage;
use stdClass;

class TwitterDirectMessageTest extends TestCase
{
    /** @var TwitterDirectMessage */
    protected $messageWithUserId;

    /** @var TwitterDirectMessage */
    protected $messageWithScreenName;

    protected $twitter;

    public function setUp()
    {
        parent::setUp();
        $this->twitter = Mockery::mock(TwitterOAuth::class);
        $this->messageWithUserId = new TwitterDirectMessage(1234, 'myMessage');
        $this->messageWithScreenName = new TwitterDirectMessage('receiver', 'myMessage');
    }

    /** @test */
    public function it_accepts_receiver_and_message_when_constructed()
    {
        $this->assertEquals(1234, $this->messageWithUserId->getReceiver($this->twitter));
        $this->assertEquals('myMessage', $this->messageWithUserId->getContent());
    }

    /** @test */
    public function it_can_get_the_content()
    {
        $this->assertEquals('myMessage', $this->messageWithUserId->getContent());
    }

    /** @test */
    public function it_can_get_the_receiver()
    {
        $this->assertEquals(1234, $this->messageWithUserId->getReceiver($this->twitter));
    }

    /** @test */
    public function it_can_get_the_receiver_for_screen_name()
    {
        $this->twitter->shouldReceive('get')->once()->with('users/show', [
            'screen_name' => 'receiver',
            'include_user_entities' => false,
            'skip_status' => true,
        ])->once()->andReturn((object)['id' => 1234]);

        $this->twitter->shouldReceive('getLastHttpCode')->once()->andReturn(200);

        $this->assertEquals(1234, $this->messageWithScreenName->getReceiver($this->twitter));
    }

    /** @test */
    public function it_can_get_the_api_endpoint()
    {
        $this->assertEquals('direct_messages/events/new', $this->messageWithUserId->getApiEndpoint());
    }

    /** @test */
    public function it_can_get_the_request_body()
    {
        $expected = [
            'event' => [
                'type' => 'message_create',
                'message_create' => [
                    'target' => [
                        'recipient_id' => 1234
                    ],
                    'message_data' => [
                        'text' => 'myMessage'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $this->messageWithUserId->getRequestBody($this->twitter));
    }
}
