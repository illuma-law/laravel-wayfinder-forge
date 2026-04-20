<?php

declare(strict_types=1);

namespace IllumaLaw\WayfinderForge;

use IllumaLaw\WayfinderForge\Commands\WayfinderForgeCommand;
use IllumaLaw\WayfinderForge\Routing\RouteDefaultParameterManager;
use Illuminate\Routing\UrlGenerator;
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

    public function packageBooted(): void
    {
        if (! config('wayfinder-forge.route_defaults.register_forget_macro', true)) {
            return;
        }

        $macroName = (string) config(
            'wayfinder-forge.route_defaults.forget_macro_name',
            'forgetTeamRouteDefaultParameters'
        );

        $defaultParameters = config(
            'wayfinder-forge.route_defaults.forget_parameters',
            ['team', 'current_team']
        );

        if (! is_array($defaultParameters)) {
            return;
        }

        /** @var array<int, string> $parameters */
        $parameters = array_values(array_filter($defaultParameters, static fn (mixed $parameter): bool => is_string($parameter)));

        UrlGenerator::macro($macroName, function () use ($parameters): void {
            /** @var UrlGenerator $this */
            RouteDefaultParameterManager::forget($this, $parameters);
        });
    }
}
