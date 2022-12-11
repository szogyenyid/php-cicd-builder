<?php

namespace szogyenyid\PhpCicdBuilder\Interfaces;

interface Arrayable
{
    public function toArray(ProviderStrategy &$strategy): array;
}
