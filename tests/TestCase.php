<?php

namespace NotificationChannels\Twitter\Test;

use Mockery as m;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        if ($container = m::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }

        m::close();
    }
}
