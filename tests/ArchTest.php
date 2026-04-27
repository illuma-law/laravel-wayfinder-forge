<?php

declare(strict_types=1);

arch('it should not use forbidden functions')
    ->expect(['dd', 'dump', 'ray', 'var_dump'])
    ->not->toBeUsed();

arch('it should be strictly typed')
    ->expect('IllumaLaw\WayfinderForge')
    ->toUseStrictTypes();

arch('it should have valid classes')
    ->expect('IllumaLaw\WayfinderForge')
    ->toBeClasses();

arch('it should follow folder structure')
    ->expect('IllumaLaw\WayfinderForge\Commands')
    ->toBeClasses()
    ->toExtend('Illuminate\Console\Command');
