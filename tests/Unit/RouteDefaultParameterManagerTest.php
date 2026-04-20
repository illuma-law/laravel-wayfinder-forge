<?php

use Illuminate\Support\Facades\URL;
use IllumaLaw\WayfinderForge\Routing\RouteDefaultParameterManager;

it('forgets selected default URL parameters', function () {
    URL::defaults([
        'team' => 'alpha',
        'current_team' => 'alpha',
        'locale' => 'en',
    ]);

    $urlGenerator = URL::getFacadeRoot();

    expect($urlGenerator)->not->toBeNull();

    RouteDefaultParameterManager::forget($urlGenerator, ['team', 'current_team']);

    expect($urlGenerator->getDefaultParameters())
        ->toHaveKey('locale', 'en')
        ->not->toHaveKey('team')
        ->not->toHaveKey('current_team');
});

it('registers the configured URL macro for clearing defaults', function () {
    URL::defaults([
        'team' => 'beta',
        'current_team' => 'beta',
        'locale' => 'pt',
    ]);

    URL::forgetTeamRouteDefaultParameters();

    $urlGenerator = URL::getFacadeRoot();

    expect($urlGenerator)->not->toBeNull();

    expect($urlGenerator->getDefaultParameters())
        ->toHaveKey('locale', 'pt')
        ->not->toHaveKey('team')
        ->not->toHaveKey('current_team');
});

