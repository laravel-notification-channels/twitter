<?php

namespace NotificationChannels\Twitter\Test;

use ArgumentCountError;
use NotificationChannels\Twitter\TwitterMessage;

class TwitterMessageTest extends TestCase
{
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
