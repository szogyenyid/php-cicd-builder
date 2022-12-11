<?php

namespace MyProject\CICD;

use szogyenyid\PhpCicdBuilder\Script;

class CompileSCSS extends Script
{
    public function __construct(
        string $inputPath = "assets",
        string $outputPath = "public",
        bool $compress = true,
        bool $addToGitFtpInclude = true
    ) {
        $this->addInitCommand('apk add npm');
        $this->addInitCommand('npm install -g sass');
        $this->addCommand('sass --update ' . $inputPath . ':' . $outputPath . ($compress ? ' --style=compressed' : ''));
        if ($addToGitFtpInclude) {
            $this->addCommand('echo "!/' . $outputPath . '/" >> .git-ftp-include;');
        }
    }
}
