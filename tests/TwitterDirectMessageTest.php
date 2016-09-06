<?php

namespace NotificationChannels\TWitter\Test;

use NotificationChannels\Twitter\TwitterDirectMessage;

class TwitterDirectMessageTest extends \PHPUnit_Framework_TestCase
{
    /** @var TwitterDirectMessage */
    protected $message;

    /** @test */
    public function it_accepts_receiver_and_message_when_constructed()
    {
        $message = new TwitterDirectMessage('receiver', 'myMessage');

        $this->assertEquals('receiver', $message->getReceiver());
        $this->assertEquals('myMessage', $message->getContent());
    }
}
