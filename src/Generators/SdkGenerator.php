<?php

namespace IllumaLaw\WayfinderForge\Generators;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use IllumaLaw\WayfinderForge\Mappers\TypeMapper;
use ReflectionMethod;
use Spatie\LaravelData\Data;

class SdkGenerator
{
    protected TypeMapper $typeMapper;

    public function __construct(TypeMapper $typeMapper)
    {
        $this->typeMapper = $typeMapper;
    }

    public function generate(): string
    {
        $routes = $this->getFilteredRoutes();
        /** @var string $client */
        $client = Config::get('wayfinder-forge.client', 'axios');

        $output = $this->generateHeader($client);
        $interfaces = [];
        $functions = [];

        foreach ($routes as $route) {
            $analysis = $this->analyzeRoute($route);

            if ($analysis['requestInterface']) {
                $interfaces[] = (string) $analysis['requestInterface'];
            }

            $functions[] = $this->generateFunction($analysis, $client);
        }

        $output .= implode("\n\n", array_unique($interfaces))."\n\n";
        $output .= implode("\n\n", $functions);

        return $output;
    }

    /**
     * @return array<int, Route>
     */
    protected function getFilteredRoutes(): array
    {
        /** @var Router $router */
        $router = app('router');
        $routes = $router->getRoutes()->getRoutes();

        /** @var array<int, string> $includePrefixes */
        $includePrefixes = Config::get('wayfinder-forge.routes.include_prefixes', ['api/*']);
        /** @var array<int, string> $excludeMiddlewares */
        $excludeMiddlewares = Config::get('wayfinder-forge.routes.exclude_middlewares', []);

        /** @var array<int, Route> $filtered */
        $filtered = array_filter($routes, function (Route $route) use ($includePrefixes, $excludeMiddlewares) {
            $uri = $route->uri();
            $matchesPrefix = false;
            foreach ($includePrefixes as $prefix) {
                if (Str::is($prefix, $uri)) {
                    $matchesPrefix = true;
                    break;
                }
            }
            if (! $matchesPrefix) {
                return false;
            }

            /** @var array<int, string> $middlewares */
            $middlewares = $route->gatherMiddleware();
            foreach ($excludeMiddlewares as $middleware) {
                if (in_array($middleware, $middlewares)) {
                    return false;
                }
            }

            return true;
        });

        return array_values($filtered);
    }

    /**
     * @return array{name: string, method: string, uri: string, params: array<int, string>, requestInterface: string|null, requestType: string}
     */
    protected function analyzeRoute(Route $route): array
    {
        $action = $route->getActionName();
        if ($action === 'Closure' || ! str_contains($action, '@')) {
            /** @var string $method */
            $method = $route->methods()[0];
            /** @var array<int, string> $params */
            $params = array_values($route->parameterNames());

            return [
                'name'             => $this->getRouteName($route),
                'method'           => strtolower($method),
                'uri'              => $route->uri(),
                'params'           => $params,
                'requestInterface' => null,
                'requestType'      => 'any',
            ];
        }

        [$controller, $method] = explode('@', $action);
        $reflection = new ReflectionMethod($controller, $method);

        $requestInterface = null;
        $requestType = 'any';

        foreach ($reflection->getParameters() as $parameter) {
            $type = $parameter->getType();
            if (! $type || ! method_exists($type, 'getName')) {
                continue;
            }

            /** @var class-string $className */
            $className = $type->getName();
            if (is_subclass_of($className, FormRequest::class)) {
                /** @var FormRequest $requestInstance */
                $requestInstance = new $className;
                if (method_exists($requestInstance, 'rules')) {
                    $interfaceName = Str::studly(str_replace('/', '_', $route->uri())).'Request';
                    /** @var array<string, mixed> $rules */
                    $rules = $requestInstance->rules();
                    $requestInterface = $this->typeMapper->mapRules($rules, $interfaceName);
                    $requestType = $interfaceName;
                }
            } elseif (is_subclass_of($className, Data::class)) {
                $requestInterface = $this->typeMapper->mapSpatieData($className);
                $requestType = (new \ReflectionClass($className))->getShortName();
            }
        }

        /** @var string $method */
        $method = $route->methods()[0];
        /** @var array<int, string> $params */
        $params = array_values($route->parameterNames());

        return [
            'name'             => $this->getRouteName($route),
            'method'           => strtolower($method),
            'uri'              => $route->uri(),
            'params'           => $params,
            'requestInterface' => $requestInterface,
            'requestType'      => $requestType,
        ];
    }

    protected function getRouteName(Route $route): string
    {
        $name = $route->getName();
        if ($name) {
            return Str::camel(str_replace('.', '_', $name));
        }

        return Str::camel(str_replace(['/', '{', '}', '-'], ['_', '', '', '_'], $route->uri()));
    }

    protected function generateHeader(string $client): string
    {
        $header = "/**\n * This file was auto-generated by Laravel Wayfinder Forge.\n * Do not modify this file manually.\n */\n\n";

        if ($client === 'axios') {
            $header .= "import axios from 'axios';\n\n";
        } elseif ($client === 'inertia') {
            $header .= "import { router } from '@inertiajs/react';\n\n";
        }

        return $header;
    }

    /**
     * @param array{name: string, method: string, uri: string, params: array<int, string>, requestInterface: string|null, requestType: string} $analysis
     */
    protected function generateFunction(array $analysis, string $client): string
    {
        $name = $analysis['name'];
        $method = $analysis['method'];
        $uri = $analysis['uri'];
        $params = $analysis['params'];
        $requestType = $analysis['requestType'];

        $args = [];
        foreach ($params as $param) {
            $args[] = "{$param}: string | number";
        }

        if (in_array($method, ['post', 'put', 'patch'])) {
            $args[] = "data: {$requestType}";
        }

        $argsString = implode(', ', $args);

        $url = $uri;
        foreach ($params as $param) {
            $url = (string) str_replace("{{$param}}", "\${{$param}}", $url);
        }

        $functionBody = '';
        if ($client === 'axios') {
            $functionBody = "    return axios.{$method}(`/{$url}`".(in_array($method, ['post', 'put', 'patch']) ? ', data' : '').');';
        } elseif ($client === 'fetch') {
            $functionBody = "    return fetch(`/{$url}`, {\n        method: '{$method}',\n".(in_array($method, ['post', 'put', 'patch']) ? "        body: JSON.stringify(data),\n" : '')."        headers: { 'Content-Type': 'application/json' }\n    }).then(res => res.json());";
        } elseif ($client === 'inertia') {
            $functionBody = "    return router.{$method}(`/{$url}`".(in_array($method, ['post', 'put', 'patch']) ? ', data' : '').');';
        }

        return "export const {$name} = ({$argsString}) => {\n{$functionBody}\n};";
    }
}
