<?php

namespace NotificationChannels\Twitter;

function mime_content_type($path)
{
    return 'video/mp4';
}

namespace NotificationChannels\Twitter\Test;

use Mockery as m;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        if ($container = m::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }

        m::close();
    }
}
