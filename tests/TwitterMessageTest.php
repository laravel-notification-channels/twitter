<?php

namespace NotificationChannels\Twitter\Test;

use ArgumentCountError;
use NotificationChannels\Twitter\TwitterMessage;

class TwitterMessageTest extends TestCase
{
    public function test_it_needs_a_content()
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

    public function test_it_returns_the_provided_content()
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

    public function test_it_has_an_is_json_request_property_with_default_value_true()
    {
        $message = new class('Foo content') extends TwitterMessage
        {
            public function getApiEndpoint(): string
            {
                return 'tweets';
            }
        };

        $this->assertTrue($message->isJsonRequest);
    }
}
