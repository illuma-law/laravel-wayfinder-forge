<?php

declare(strict_types=1);

use IllumaLaw\WayfinderForge\Routing\RoutePrefixCollector;

it('collects unique sorted static route prefixes from uris', function () {
    $prefixes = RoutePrefixCollector::fromUris([
        'dashboard',
        'dashboard/stats',
        'teams/{team}/members',
        'api/v1/users',
        'api/v1/projects',
        '',
        'login',
        'login',
    ]);

    expect($prefixes)->toBe([
        'api',
        'dashboard',
        'login',
        'teams',
    ]);
});
