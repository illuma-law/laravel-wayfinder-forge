<?php

declare(strict_types=1);

namespace IllumaLaw\WayfinderForge\Routing;

use Illuminate\Routing\RouteUrlGenerator;
use Illuminate\Routing\UrlGenerator;
use ReflectionMethod;

final class RouteDefaultParameterManager
{
    /**
     * @param  array<int, string>  $parameters
     */
    public static function forget(UrlGenerator $urlGenerator, array $parameters): void
    {
        $routeUrlGenerator = self::routeUrlGenerator($urlGenerator);
        $defaults = $routeUrlGenerator?->defaultParameters ?? [];

        foreach ($parameters as $parameter) {
            unset($defaults[$parameter]);
        }

        if ($routeUrlGenerator instanceof RouteUrlGenerator) {
            $routeUrlGenerator->defaultParameters = $defaults;

            return;
        }

        $urlGenerator->defaults($defaults);
    }

    private static function routeUrlGenerator(UrlGenerator $urlGenerator): ?RouteUrlGenerator
    {
        $routeUrlMethod = new ReflectionMethod($urlGenerator, 'routeUrl');
        $routeUrlMethod->setAccessible(true);

        $routeUrlGenerator = $routeUrlMethod->invoke($urlGenerator);

        return $routeUrlGenerator instanceof RouteUrlGenerator ? $routeUrlGenerator : null;
    }
}
