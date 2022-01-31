<?php

namespace NotificationChannels\Twitter\Test;

use Abraham\TwitterOAuth\TwitterOAuth;
use ArgumentCountError;
use Mockery;
use NotificationChannels\Twitter\TwitterDirectMessage;
use NotificationChannels\Twitter\TwitterMessage;

class TwitterMessageTest extends TestCase
{
//    public function setUp(): void
//    {
//        parent::setUp();
//        $this->twitter = Mockery::mock(TwitterOAuth::class);
//        $this->messageWithUserId = new TwitterDirectMessage(1234, 'myMessage');
//        $this->messageWithScreenName = new TwitterDirectMessage('receiver', 'myMessage');
//    }

    /** @test */
    public function it_needs_a_content()
    {
        $this->expectException(ArgumentCountError::class);

        $message = new class extends TwitterMessage
        {
            public function getApiEndpoint(): string
            {
                return 'status/update';
            }
        };
    }

    /** @test */
    public function it_returns_the_provided_content()
    {
        $message = new class('Foo content') extends TwitterMessage
        {
            public function getApiEndpoint(): string
            {
                return 'status/update';
            }
        };

        $this->assertEquals('Foo content', $message->getContent());
    }

    /** @test */
    public function it_has_an_is_json_request_property_with_default_value_false()
    {
        $message = new class('Foo content') extends TwitterMessage
        {
            public function getApiEndpoint(): string
            {
                return 'status/update';
            }
        };

        $this->assertObjectHasAttribute('isJsonRequest', $message);
        $this->assertFalse($message->isJsonRequest);
    }
}
