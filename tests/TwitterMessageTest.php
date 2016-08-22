<?php

namespace NotificationChannels\TWitter\Test;

use NotificationChannels\Twitter\TwitterMessage;

class TwitterMessageTest extends \PHPUnit_Framework_TestCase
{
    /** @var TwitterMessage */
    protected $message;

    /** @test */
    public function it_provides_a_factory_method()
    {
        $message = TwitterMessage::create('myMessage');

        $this->assertEquals('myMessage', $message->getContent());
    }

    /** @test */
    public function it_accepts_a_message_when_constructing_a_message()
    {
        $message = new TwitterMessage('myMessage');

        $this->assertEquals('myMessage', $message->getContent());
    }

}