<?php

namespace szogyenyid\PhpCicdBuilder\Interfaces;

interface ProviderStrategy
{
    public function pipelineToArray(array &$variables, array &$steps): array;
    public function stepToArray(string $name, ?string $deployment, bool $isManual, array &$initScripts, array &$scripts): array;
    public function variablesToArray(string $name, ?string $defaultValue, ?array &$allowedValues): array;
}
