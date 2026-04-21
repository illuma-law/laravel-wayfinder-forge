---
description: Auto-generates strongly typed TypeScript SDKs from Laravel FormRequests and Spatie Data objects
---

# laravel-wayfinder-forge

Auto-generates strongly typed frontend TypeScript interfaces and request types from Laravel backend `FormRequest` classes and `Spatie\LaravelData\Data` objects. Works alongside `laravel/wayfinder`.

## Namespace

`IllumaLaw\WayfinderForge`

## Key Command

```bash
php artisan wayfinder-forge:generate
```

Outputs TypeScript files to the configured output directory (default: `resources/js/types/forge/`).

## Config

Publish: `php artisan vendor:publish --tag="wayfinder-forge-config"`

```php
return [
    'output_path' => resource_path('js/types/forge'),
    'sources'     => [
        app_path('Http/Requests'),
        app_path('Data'),
    ],
    'clients' => ['axios', 'fetch'], // or just one
];
```

## FormRequest → TypeScript

Given:
```php
class StoreCaseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
```

Generates:
```ts
export interface StoreCaseRequest {
  title: string;
  description?: string | null;
}
```

## Spatie Laravel Data → TypeScript

Given:
```php
class CaseData extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
    ) {}
}
```

Generates equivalent TypeScript interface with strict typing.

## Usage in Frontend

```ts
import type { StoreCaseRequest } from '@/types/forge/StoreCaseRequest';
import { store } from '@/actions/CasesController'; // from laravel/wayfinder

const payload: StoreCaseRequest = { title: 'New Case' };
await store.post(payload);
```

## Notes

- Run `wayfinder-forge:generate` after changing any `FormRequest` or `Data` class.
- Add to Vite HMR or CI to keep types in sync.
- For Inertia pages, import generated types alongside Wayfinder route functions.
