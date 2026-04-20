<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use IllumaLaw\WayfinderForge\Generators\SdkGenerator;
use IllumaLaw\WayfinderForge\Tests\Mocks\MockController;
use IllumaLaw\WayfinderForge\Tests\Mocks\MockData;

it('generates a typescript sdk for axios', function () {
    Route::post('api/test', [MockController::class, 'store'])->name('test.store');

    $generator = app(SdkGenerator::class);
    $sdk = $generator->generate();

    expect($sdk)
        ->toContain("import axios from 'axios';")
        ->toContain('export interface ApiTestRequest {')
        ->toContain('title: string;')
        ->toContain('export const testStore = (data: ApiTestRequest) => {')
        ->toContain("return axios.post(`/api/test`, data);");
});

it('generates a typescript sdk for fetch', function () {
    Config::set('wayfinder-forge.client', 'fetch');
    Route::post('api/test', [MockController::class, 'store'])->name('test.store');

    $generator = app(SdkGenerator::class);
    $sdk = $generator->generate();

    expect($sdk)
        ->toContain("export const testStore = (data: ApiTestRequest) => {")
        ->toContain("return fetch(`/api/test`")
        ->toContain("method: 'post'")
        ->toContain("body: JSON.stringify(data)");
});

it('generates a typescript sdk for inertia', function () {
    Config::set('wayfinder-forge.client', 'inertia');
    Route::post('api/test', [MockController::class, 'store'])->name('test.store');

    $generator = app(SdkGenerator::class);
    $sdk = $generator->generate();

    expect($sdk)
        ->toContain("import { router } from '@inertiajs/react';")
        ->toContain("return router.post(`/api/test`, data);");
});

it('handles route parameters', function () {
    Route::get('api/users/{user}', function () { return 'ok'; })->name('users.show');

    $generator = app(SdkGenerator::class);
    $sdk = $generator->generate();

    expect($sdk)
        ->toContain('export const usersShow = (user: string | number) => {')
        ->toContain('return axios.get(`/api/users/${user}`);');
});

it('filters routes by prefix', function () {
    Route::get('api/include', function () { return 'ok'; })->name('include');
    Route::get('other/exclude', function () { return 'ok'; })->name('exclude');

    $generator = app(SdkGenerator::class);
    $sdk = $generator->generate();

    expect($sdk)
        ->toContain('export const include = () => {')
        ->not->toContain('export const exclude = () => {');
});

it('filters routes by middleware', function () {
    Config::set('wayfinder-forge.routes.exclude_middlewares', ['secret']);

    Route::get('api/public', function () { return 'ok'; })->name('public');
    Route::get('api/private', function () { return 'ok'; })->name('private')->middleware('secret');

    $generator = app(SdkGenerator::class);
    $sdk = $generator->generate();

    expect($sdk)
        ->toContain('export const public = () => {')
        ->not->toContain('export const private = () => {');
});
