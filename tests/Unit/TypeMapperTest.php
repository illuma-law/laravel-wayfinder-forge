<?php

use IllumaLaw\WayfinderForge\Mappers\TypeMapper;
use IllumaLaw\WayfinderForge\Tests\Mocks\MockData;

it('can map basic validation rules to typescript interface', function () {
    $mapper = new TypeMapper;
    $rules = [
        'name'      => 'required|string',
        'age'       => 'nullable|integer',
        'is_active' => 'boolean',
    ];

    $ts = $mapper->mapRules($rules, 'UserRequest');

    expect($ts)->toContain('export interface UserRequest {')
        ->toContain('name: string;')
        ->toContain('age?: number | null;')
        ->toContain('is_active?: boolean;');
});

it('can map spatie data to typescript interface', function () {
    $mapper = new TypeMapper;

    $ts = $mapper->mapSpatieData(MockData::class);

    expect($ts)->toContain('export interface MockData {')
        ->toContain('name: string;')
        ->toContain('age?: number;')
        ->toContain('is_active: boolean;');
});
