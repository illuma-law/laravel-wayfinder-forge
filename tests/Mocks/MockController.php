<?php

namespace IllumaLaw\WayfinderForge\Tests\Mocks;

use Illuminate\Routing\Controller;

class MockController extends Controller
{
    public function store(MockRequest $request)
    {
        return 'ok';
    }
}
