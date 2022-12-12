<?php

namespace szogyenyid\PhpCicdBuilder\ExampleScripts;

use szogyenyid\PhpCicdBuilder\Script;

class CodingStandard extends Script
{
    public function __construct(string $path = "src", string $standard = "PSR12")
    {
        $this->addInitCommand("apk add php8");
        $this->addInitCommand("apk add php8-phar");
        $this->addInitCommand("apk add php8-tokenizer");
        $this->addInitCommand("apk add php8-xmlwriter");
        $this->addInitCommand("apk add php8-simplexml");
        $this->addInitCommand("wget https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar");
        $this->addCommand('php phpcs.phar -s --report=summary --extensions=php --standard=' . $standard . ' ' . $path);
    }
}
