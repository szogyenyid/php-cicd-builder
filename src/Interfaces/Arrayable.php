<?php

namespace Soegaeni\PhpCicdBuilder\Interfaces;

interface Arrayable
{
    public function toArray(ProviderStrategy &$strategy): array;
}
