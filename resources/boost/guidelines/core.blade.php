# illuma-law/laravel-wayfinder-forge

Auto-generate strongly typed TypeScript SDKs from Laravel Wayfinder routes.

## Usage

Run the forge command:

```bash
php artisan wayfinder:forge
```

## Features

- **TypeScript Generation**: Converts `FormRequest` or Spatie `Data` objects into TypeScript interfaces.
- **Client Support**: Formats SDK for `axios`, `fetch`, or `inertia`.
- **Validation Sync**: Keeps frontend types in sync with backend validation rules.

## Example

**Backend (Route + Request)**:
```php
// Route name: posts.store
Route::post('api/posts', [PostController::class, 'store'])->name('posts.store');

class StorePostRequest extends FormRequest {
    public function rules(): array { return ['title' => 'required|string']; }
}
```

**Generated TypeScript**:
```typescript
export interface ApiPostsRequest { title: string; }
export const postsStore = (data: ApiPostsRequest) => axios.post(`/api/posts`, data);
```

## Configuration

Publish config: `php artisan vendor:publish --tag="laravel-wayfinder-forge-config"`

Options in `config/wayfinder-forge.php`:
- `output_path`: Default `resources/js/api/sdk.ts`.
- `client`: `axios`, `fetch`, or `inertia`.
- `routes.include_prefixes`: e.g., `api/*`.
- `use_spatie_data`: Boolean.
