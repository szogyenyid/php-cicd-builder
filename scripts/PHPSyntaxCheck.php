<?php

namespace MyProject\CICD;

use Soegaeni\PhpCicdBuilder\Script;

class PHPSyntaxCheck extends Script
{
    public function __construct(string $path = 'src')
    {
        $this->addInitCommand('apk add php8');
        $this->addCommand('for file in $(find ' . $path . ' -type f -name "*.php"); do php -l $file; done');
    }
}
