<?php

return [
    /**
     * The output path for the generated TypeScript SDK file.
     */
    'output_path' => resource_path('js/api/sdk.ts'),

    /**
     * Route configuration.
     */
    'routes' => [
        /**
         * Only include routes that start with these prefixes.
         * Use '*' as a wildcard.
         */
        'include_prefixes' => [
            'api/*',
        ],

        /**
         * Exclude routes that have these middlewares.
         */
        'exclude_middlewares' => [
            'debugbar',
        ],
    ],

    /**
     * The HTTP client to format the SDK for.
     * Supported: 'axios', 'fetch', 'inertia'
     */
    'client' => 'axios',

    /**
     * Whether to generate interfaces for Spatie Laravel Data objects.
     */
    'use_spatie_data' => true,
];
