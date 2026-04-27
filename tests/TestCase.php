<?php

declare(strict_types=1);

namespace IllumaLaw\WayfinderForge\Tests;

use IllumaLaw\WayfinderForge\WayfinderForgeServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            WayfinderForgeServiceProvider::class,
        ];
    }
}
