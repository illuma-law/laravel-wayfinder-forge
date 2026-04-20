<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

it('can run the wayfinder forge command', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'sdk');
    Config::set('wayfinder-forge.output_path', $tempFile);

    Artisan::call('wayfinder:forge');

    expect(file_exists($tempFile))->toBeTrue();
    expect(file_get_contents($tempFile))->toContain('Laravel Wayfinder Forge');

    unlink($tempFile);
});
