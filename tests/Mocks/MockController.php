<?php

declare(strict_types=1);

namespace IllumaLaw\WayfinderForge\Tests\Mocks;

use Illuminate\Routing\Controller;

class MockController extends Controller
{
    public function store(MockRequest $request): string
    {
        return 'ok';
    }
}
