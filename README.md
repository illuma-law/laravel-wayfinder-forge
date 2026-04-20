# Laravel Wayfinder Forge

[![Latest Version on Packagist](https://img.shields.io/packagist/v/illuma-law/laravel-wayfinder-forge.svg?style=flat-square)](https://packagist.org/packages/illuma-law/laravel-wayfinder-forge)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/illuma-law/laravel-wayfinder-forge/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/illuma-law/laravel-wayfinder-forge/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/illuma-law/laravel-wayfinder-forge.svg?style=flat-square)](https://packagist.org/packages/illuma-law/laravel-wayfinder-forge)

Auto-generate strongly typed frontend SDKs from your Laravel backend routes and FormRequests.

Wayfinder Forge ensures your frontend client code stays perfectly in sync with your backend validation rules. It parses your Laravel routes, reflects on their `FormRequest` or `Spatie\Data` classes, and automatically generates a ready-to-use TypeScript SDK.

## Features

- **TypeScript Interface Generation:** Automatically converts Laravel validation rules (`required`, `string`, `nullable`, `array`) into accurate TypeScript interfaces.
- **Spatie Laravel Data Support:** First-class support for `spatie/laravel-data` DTOs.
- **Multiple Client Support:** Generates SDKs formatted for `axios`, standard `fetch`, or `inertia`.
- **Customizable Output:** Easily define which routes to include/exclude via prefixes and middleware filters.

## Installation

You can install the package via composer:

```bash
composer require illuma-law/laravel-wayfinder-forge --dev
```

Publish the config file:

```bash
php artisan vendor:publish --tag="laravel-wayfinder-forge-config"
```

## Configuration

The published `config/wayfinder-forge.php` allows you to customize the output behavior:

```php
return [
    /**
     * The output path for the generated TypeScript SDK file.
     */
    'output_path' => resource_path('js/api/sdk.ts'),

    /**
     * Define which routes should be processed.
     */
    'routes' => [
        // Only include routes matching these prefixes
        'include_prefixes' => [
            'api/*',
        ],

        // Exclude routes with specific middleware (like internal debug tools)
        'exclude_middlewares' => [
            'debugbar',
        ],
    ],

    /**
     * The HTTP client template to use for the generated SDK.
     * Supported: 'axios', 'fetch', 'inertia'
     */
    'client' => 'axios',

    /**
     * Enable support for spatie/laravel-data objects.
     */
    'use_spatie_data' => true,
];
```

## Usage & Integration

Generate your TypeScript SDK by running the forge command:

```bash
php artisan wayfinder:forge
```

*Tip: Add this command to your `package.json` build scripts so it runs automatically during development.*

### Example: FormRequests

#### Laravel Controller & Request

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'boolean', // Optional by default unless 'required' is present
        ];
    }
}

// routes/api.php
Route::post('api/posts', [PostController::class, 'store'])->name('posts.store');
```

#### Generated TypeScript SDK (`resources/js/api/sdk.ts`)

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

### Example: Spatie Laravel Data

If you are using `spatie/laravel-data` to type-hint your controllers instead of FormRequests, Forge handles that automatically.

#### Laravel Controller & Data Object

```php
namespace App\Data;

use Spatie\LaravelData\Data;

class UserData extends Data
{
    public string $name;
    public ?int $age;
}

// routes/api.php
Route::post('api/users', function (UserData $userData) { 
    // ...
});
```

#### Generated TypeScript SDK

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

Run the package tests:

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
