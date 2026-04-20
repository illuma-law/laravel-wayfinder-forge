# Laravel Wayfinder Forge

[![Latest Version on Packagist](https://img.shields.io/packagist/v/illuma-law/laravel-wayfinder-forge.svg?style=flat-square)](https://packagist.org/packages/illuma-law/laravel-wayfinder-forge)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/illuma-law/laravel-wayfinder-forge/ci.yml?branch=main&label=tests&style=flat-square)](https://github.com/illuma-law/laravel-wayfinder-forge/actions?query=workflow%3ACI+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/illuma-law/laravel-wayfinder-forge/ci.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/illuma-law/laravel-wayfinder-forge/actions?query=workflow%3ACI+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/illuma-law/laravel-wayfinder-forge.svg?style=flat-square)](https://packagist.org/packages/illuma-law/laravel-wayfinder-forge)

Auto-generate strongly typed frontend SDKs from your Laravel Wayfinder routes.

Laravel Wayfinder Forge analyzes your Laravel routes (following Wayfinder conventions), parses their `FormRequest` or Spatie `Data` objects, and generates a TypeScript SDK. This ensures your frontend stays in sync with your backend validation rules and route parameters.

## Installation

You can install the package via composer:

```bash
composer require illuma-law/laravel-wayfinder-forge
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-wayfinder-forge-config"
```

## Configuration

This is the contents of the published config file:

```php
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
```

## Usage

Run the forge command to generate your TypeScript SDK:

```bash
php artisan wayfinder:forge
```

### Example

#### Laravel Controller & Request

```php
// app/Http/Requests/StorePostRequest.php
class StorePostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ];
    }
}

// app/Http/Controllers/PostController.php
class PostController extends Controller
{
    public function store(StorePostRequest $request)
    {
        // ...
    }
}

// routes/api.php
Route::post('api/posts', [PostController::class, 'store'])->name('posts.store');
```

#### Generated TypeScript SDK

```typescript
export interface ApiPostsRequest {
    title: string;
    content: string;
    is_published?: boolean;
}

export const postsStore = (data: ApiPostsRequest) => {
    return axios.post(`/api/posts`, data);
};
```

### Spatie Data Example

If your route uses a Spatie Data object:

```php
class UserData extends Data
{
    public string $name;
    public ?int $age;
}

Route::post('api/users', function (UserData $userData) { ... });
```

It generates:

```typescript
export interface UserData {
    name: string;
    age?: number;
}

export const apiUsers = (data: UserData) => {
    return axios.post(`/api/users`, data);
};
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [illuma-law](https://github.com/illuma-law)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
