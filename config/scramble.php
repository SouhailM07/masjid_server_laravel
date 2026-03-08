<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [
    /*
     * Your API info. This info gets used in the generated documentation.
     */
    'api' => [
        'info' => [
            'version' => env('API_VERSION', '1.0.0'),
            'description' => 'My Laravel API Documentation',
        ],
    ],

    /*
     * The route path to access the docs UI
     */
    'ui' => [
        'enabled' => true,
        'route' => '/docs/api',  // URL where docs appear
    ],

    /*
     * Settings for the API routes that should be documented
     */
    'routes' => [
        'domain' => null,
        'prefixes' => ['api/*'],  // Which routes to document
        'exclude' => [],          // Routes to exclude
    ],

    /*
     * Middleware for the docs UI (protect with auth if needed)
     */
    'middleware' => [
        'web',
        RestrictedDocsAccess::class, // Remove this to make public
    ],
];