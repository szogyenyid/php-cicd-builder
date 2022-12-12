<?php

namespace szogyenyid\PhpCicdBuilder\ExampleScripts;

use szogyenyid\PhpCicdBuilder\Script;

class ComposerInstall extends Script
{
    public function __construct()
    {
        $this->addInitCommand("apk add php8");
        $this->addInitCommand('wget https://github.com/composer/composer/releases/download/2.4.4/composer.phar');
        $this->addCommand('php composer.phar install');
        $this->addCommand('echo "!/vendor/" >> .git-ftp-include;');
    }
}
