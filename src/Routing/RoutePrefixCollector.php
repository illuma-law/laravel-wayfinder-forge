<?php

declare(strict_types=1);

namespace IllumaLaw\WayfinderForge\Routing;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class RoutePrefixCollector
{
    /**
     * @param  iterable<string>  $uris
     * @return array<int, string>
     */
    public static function fromUris(iterable $uris): array
    {
        /** @var Collection<int, string> $uriCollection */
        $uriCollection = collect($uris);

        return $uriCollection
            ->map(static fn (string $uri): string => explode('/', $uri)[0])
            ->reject(static fn (string $uri): bool => Str::contains($uri, '{'))
            ->filter(static fn (string $uri): bool => $uri !== '')
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }
}
