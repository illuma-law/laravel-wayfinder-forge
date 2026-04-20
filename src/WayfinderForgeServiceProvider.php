<?php

declare(strict_types=1);

namespace IllumaLaw\WayfinderForge;

use IllumaLaw\WayfinderForge\Commands\WayfinderForgeCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class WayfinderForgeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-wayfinder-forge')
            ->hasConfigFile()
            ->hasCommand(WayfinderForgeCommand::class);
    }
}
