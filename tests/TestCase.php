<?php

namespace NotificationChannels\Twitter\Test;

use Mockery;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{

    function tearDown()
    {
        parent::tearDown();

        if ($container = \Mockery::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }

        Mockery::close();
    }

}