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
     * Generic URL default parameter helpers used to avoid stale route defaults in long-lived workers.
     */
    'route_defaults' => [
        /**
         * Register a UrlGenerator macro for clearing selected default parameters.
         */
        'register_forget_macro' => true,

        /**
         * Name of the generated UrlGenerator macro.
         */
        'forget_macro_name' => 'forgetTeamRouteDefaultParameters',

        /**
         * Default parameter keys removed by the generated macro.
         */
        'forget_parameters' => [
            'team',
            'current_team',
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
