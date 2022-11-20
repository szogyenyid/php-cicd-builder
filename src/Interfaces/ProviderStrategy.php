<?php

namespace Soegaeni\PhpCicdBuilder\Interfaces;

interface ProviderStrategy
{
    public function pipelineToArray(array &$variables, array &$steps): array;
    public function stepToArray(string $name, bool $isManual, array &$initScripts, array &$scripts): array;
    public function variablesToArray(string $name, ?string $defaultValue, ?array &$allowedValues): array;
}
