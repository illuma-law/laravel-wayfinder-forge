<?php

declare(strict_types=1);

namespace IllumaLaw\WayfinderForge\Support\Http;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class InertiaExternalRedirect
{
    private const INERTIA_HEADER = 'X-Inertia';

    private const INERTIA_LOCATION_HEADER = 'X-Inertia-Location';

    /**
     * @param  Request  $request  The current HTTP request (checked for the Inertia header).
     * @param  string  $absoluteUrl  A fully qualified URL (https) to an external origin.
     */
    public static function to(Request $request, string $absoluteUrl): Response
    {
        if ($request->header(self::INERTIA_HEADER)) {
            return new Response('', 409, [self::INERTIA_LOCATION_HEADER => $absoluteUrl]);
        }

        return new RedirectResponse($absoluteUrl, 303);
    }
}
