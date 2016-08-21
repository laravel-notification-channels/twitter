<?php

namespace NotificationChannels\TWitter\Test;

use NotificationChannels\Twitter\TwitterMessage;

class TwitterMessageTest extends \PHPUnit_Framework_TestCase
{
    protected $message;


    /** @test */
    public function it_accepts_a_message_when_constructing_a_message()
    {
        $message = new TwitterMessage('myMessage');

        $this->assertEquals('myMessage', $message->getContent());
    }

}