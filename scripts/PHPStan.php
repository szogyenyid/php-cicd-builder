<?php

namespace MyProject\CICD;

use szogyenyid\PhpCicdBuilder\Script;

class PHPStan extends Script
{
    public function __construct(int $level = 1, string $path = "src", string $memoryLimit = "1G")
    {
        $this->addInitCommand("apk add php8");
        $this->addInitCommand("apk add php8-phar");
        $this->addInitCommand("apk add php8-json");
        $this->addInitCommand("apk add php8-tokenizer");
        $this->addInitCommand("apk add php8-ctype");
        $this->addInitCommand("apk add php8-mbstring");
        $this->addInitCommand("wget https://github.com/phpstan/phpstan/releases/download/1.2.0/phpstan.phar");
        $this->addCommand('php phpstan.phar --no-progress --memory-limit=' . $memoryLimit . ' --level=' . $level . ' analyse ' . $path);
    }
}
