<?php

declare(strict_types=1);

namespace IllumaLaw\WayfinderForge\Commands;

use IllumaLaw\WayfinderForge\Generators\SdkGenerator;
use Illuminate\Console\Command;

class WayfinderForgeCommand extends Command
{
    protected $signature = 'wayfinder:forge';

    protected $description = 'Generate a TypeScript SDK from Laravel Wayfinder routes';

    public function handle(SdkGenerator $generator): int
    {
        $this->info('Generating Wayfinder SDK...');

        $sdk = $generator->generate();
        /** @var string $path */
        $path = config('wayfinder-forge.output_path');

        $directory = dirname($path);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, $sdk);

        $this->info("SDK generated successfully at: {$path}");

        return self::SUCCESS;
    }
}
