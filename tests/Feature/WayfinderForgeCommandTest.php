<?php

use IllumaLaw\WayfinderForge\Tests\Mocks\MockController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

afterEach(function () {
    if (isset($this->outputPath) && File::exists($this->outputPath)) {
        File::delete($this->outputPath);
    }
});

it('can generate sdk for basic routes', function () {
    $this->outputPath = __DIR__.'/sdk.ts';
    Config::set('wayfinder-forge.output_path', $this->outputPath);
    Config::set('wayfinder-forge.routes.include_prefixes', ['api/*']);

    Route::get('api/users', function () {})->name('users.index');
    Route::post('api/users', function () {})->name('users.store');
    Route::get('api/users/{user}', function ($user) {})->name('users.show');

    $this->artisan('wayfinder:forge')
        ->assertSuccessful();

    expect(File::exists($this->outputPath))->toBeTrue();
    $content = File::get($this->outputPath);

    expect($content)->toContain('export const usersIndex = () => {')
        ->toContain('export const usersStore = (data: any) => {')
        ->toContain('export const usersShow = (user: string | number) => {')
        ->toContain('axios.get(`/api/users`)')
        ->toContain('axios.post(`/api/users`, data)')
        ->toContain('axios.get(`/api/users/${user}`)');
});

it('filters routes based on prefix', function () {
    $this->outputPath = __DIR__.'/sdk_filter.ts';
    Config::set('wayfinder-forge.output_path', $this->outputPath);
    Config::set('wayfinder-forge.routes.include_prefixes', ['api/*']);

    Route::get('api/users', function () {})->name('api.users');
    Route::get('web/users', function () {})->name('web.users');

    $this->artisan('wayfinder:forge')
        ->assertSuccessful();

    $content = File::get($this->outputPath);
    expect($content)->toContain('apiUsers')
        ->not->toContain('webUsers');
});

it('can generate sdk for routes with form requests', function () {
    $this->outputPath = __DIR__.'/sdk_request.ts';
    Config::set('wayfinder-forge.output_path', $this->outputPath);
    Config::set('wayfinder-forge.routes.include_prefixes', ['api/*']);

    Route::post('api/posts', [MockController::class, 'store'])->name('posts.store');

    $this->artisan('wayfinder:forge')
        ->assertSuccessful();

    $content = File::get($this->outputPath);
    expect($content)->toContain('export interface ApiPostsRequest {')
        ->toContain('title: string;')
        ->toContain('export const postsStore = (data: ApiPostsRequest) => {')
        ->toContain('axios.post(`/api/posts`, data)');
});
