<?php

namespace NotificationChannels\Twitter\Test;

use NotificationChannels\Twitter\TwitterDirectMessage;

class TwitterDirectMessageTest extends TestCase
{
    /** @var TwitterDirectMessage */
    protected $message;

    public function setUp()
    {
        parent::setUp();
        $this->message = new TwitterDirectMessage('receiver', 'myMessage');
    }

    /** @test */
    public function it_accepts_receiver_and_message_when_constructed()
    {
        $this->assertEquals('receiver', $this->message->getReceiver());
        $this->assertEquals('myMessage', $this->message->getContent());
    }

    /** @test */
    public function it_can_get_the_content()
    {
        $this->assertEquals('myMessage', $this->message->getContent());
    }

    /** @test */
    public function it_can_get_the_receiver()
    {
        $this->assertEquals('receiver', $this->message->getReceiver());
    }

    /** @test */
    public function it_can_get_the_api_endpoint()
    {
        $this->assertEquals('direct_messages/new', $this->message->getApiEndpoint());
    }

    /** @test */
    public function it_can_get_the_request_body()
    {
        $expected = [
            'screen_name' => 'receiver',
            'text' => 'myMessage',
        ];
        $this->assertEquals($expected, $this->message->getRequestBody());
    }
}
