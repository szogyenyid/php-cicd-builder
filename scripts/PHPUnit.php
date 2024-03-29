<?php

namespace szogyenyid\PhpCicdBuilder\ExampleScripts;

use szogyenyid\PhpCicdBuilder\Script;

class PHPUnit extends Script
{
    public function __construct(string $path = "tests")
    {
        $this->addInitCommand("apk add php8 php8-dom php8-json php8-mbstring php8-tokenizer php8-xml php8-xmlwriter php8-phar");
        $this->addInitCommand("wget https://phar.phpunit.de/phpunit-9.5.phar");
        $this->addCommand('php phpunit-9.5.phar --no-configuration --bootstrap vendor/autoload.php ' . $path);
    }
}
