<?php

namespace IllumaLaw\WayfinderForge\Tests\Mocks;

use Spatie\LaravelData\Data;

class MockData extends Data
{
    public string $name;

    public ?int $age;

    public bool $is_active;
}
