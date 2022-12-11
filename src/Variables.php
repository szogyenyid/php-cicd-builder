<?php

namespace szogyenyid\PhpCicdBuilder;

use szogyenyid\PhpCicdBuilder\Interfaces\Arrayable;
use szogyenyid\PhpCicdBuilder\Interfaces\ProviderStrategy;

class Variables implements Arrayable
{
    private string $name;
    private ?array $allowedValues = null;
    private ?string $default = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
    public function __toString(): string
    {
        return '${' . $this->name . '}';
    }
    public function withAllowedValues(array $values): Variables
    {
        if (!is_null($this->default) && !in_array($this->default, $values)) {
            throw new \Exception("Default value is already set, but allowed values do not include it.");
        }
        $this->allowedValues = $values;
        return $this;
    }
    public function withDefault(string $default): Variables
    {
        if (!is_null($this->allowedValues) && !in_array($default, $this->allowedValues)) {
            throw new \Exception("Default variable value must be in the allowed values array.");
        }
        $this->default = $default;
        return $this;
    }
    public function toArray(ProviderStrategy &$strategy): array
    {
        return $strategy->variablesToArray($this->name, $this->default, $this->allowedValues);
    }
}
