<?php

namespace JoeCianflone\ModelProperties\Tests;

use JoeCianflone\ModelProperties\ModelPropertiesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            ModelPropertiesServiceProvider::class,
        ];
    }
}
