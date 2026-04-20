<?php

namespace IllumaLaw\WayfinderForge\Tests\Mocks;

use Illuminate\Foundation\Http\FormRequest;

class MockRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return ['title' => 'required|string'];
    }
}
