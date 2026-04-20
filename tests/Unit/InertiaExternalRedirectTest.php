<?php

use Illuminate\Http\Request;
use IllumaLaw\WayfinderForge\Support\Http\InertiaExternalRedirect;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

it('returns an inertia location response for inertia requests', function () {
    $request = Request::create('/billing/checkout', 'POST');
    $request->headers->set('X-Inertia', 'true');

    $response = InertiaExternalRedirect::to($request, 'https://billing.example.test/session');

    expect($response)
        ->toBeInstanceOf(Response::class)
        ->and($response->getStatusCode())->toBe(409)
        ->and($response->headers->get('X-Inertia-Location'))
        ->toBe('https://billing.example.test/session');
});

it('returns a standard redirect response for non inertia requests', function () {
    $request = Request::create('/billing/checkout', 'POST');

    $response = InertiaExternalRedirect::to($request, 'https://billing.example.test/session');

    expect($response)
        ->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getStatusCode())->toBe(303)
        ->and($response->headers->get('location'))->toBe('https://billing.example.test/session');
});
