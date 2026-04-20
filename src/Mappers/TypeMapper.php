<?php

namespace IllumaLaw\WayfinderForge\Mappers;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Spatie\LaravelData\Data;

class TypeMapper
{
    /**
     * @param array<string, mixed> $rules
     */
    public function mapRules(array $rules, string $name): string
    {
        $fields = [];

        foreach ($rules as $field => $fieldRules) {
            if (is_string($fieldRules)) {
                $fieldRules = explode('|', $fieldRules);
            }

            if (! is_array($fieldRules)) {
                $fieldRules = [];
            }

            /** @var array<int, string> $stringRules */
            $stringRules = array_filter($fieldRules, fn ($rule) => is_string($rule));

            $type = $this->getTsTypeFromRules($stringRules);
            $isOptional = ! in_array('required', $stringRules);
            $isNullable = in_array('nullable', $stringRules);

            $fieldName = $isOptional ? "{$field}?" : $field;
            $finalType = $isNullable ? "{$type} | null" : $type;

            $fields[] = "    {$fieldName}: {$finalType};";
        }

        return "export interface {$name} {\n".implode("\n", $fields)."\n}";
    }

    /**
     * @param class-string<Data>|Data $class
     */
    public function mapSpatieData(string|Data $class): string
    {
        if (! is_string($class) || ! class_exists($class) || ! is_subclass_of($class, Data::class)) {
            return '';
        }

        $reflection = new ReflectionClass($class);
        $className = $reflection->getShortName();
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $fields = [];
        foreach ($properties as $property) {
            $name = $property->getName();
            $type = $this->getTsTypeFromReflectionType($property->getType());
            $isOptional = $property->getType()?->allowsNull() ?? true;

            $fieldName = $isOptional ? "{$name}?" : $name;
            $fields[] = "    {$fieldName}: {$type};";
        }

        return "export interface {$className} {\n".implode("\n", $fields)."\n}";
    }

    /**
     * @param array<int, string> $rules
     */
    protected function getTsTypeFromRules(array $rules): string
    {
        if (in_array('integer', $rules) || in_array('numeric', $rules)) {
            return 'number';
        }

        if (in_array('boolean', $rules)) {
            return 'boolean';
        }

        if (in_array('array', $rules)) {
            return 'any[]';
        }

        return 'string';
    }

    protected function getTsTypeFromReflectionType(mixed $type): string
    {
        if (! $type instanceof ReflectionNamedType) {
            return 'any';
        }

        $typeName = $type->getName();

        return match ($typeName) {
            'int', 'float' => 'number',
            'bool'   => 'boolean',
            'array'  => 'any[]',
            'string' => 'string',
            default  => 'any',
        };
    }
}
