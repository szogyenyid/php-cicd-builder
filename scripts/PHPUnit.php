<?php

namespace MyProject\CICD;

use szogyenyid\PhpCicdBuilder\Script;

class PHPUnit extends Script
{
    public function __construct(string $path = "tests")
    {
        $this->addInitCommand("apk add php8");
        $this->addInitCommand("wget https://phar.phpunit.de/phpunit-9.5.phar");
        $this->addCommand('php phpunit-9.5.phar --no-configuration --bootstrap vendor/autoload.php ' . $path);
    }
}
