<?php

namespace szogyenyid\PhpCicdBuilder\ExampleScripts;

use szogyenyid\PhpCicdBuilder\Script;

class CompileSCSS extends Script
{
    public function __construct(
        string $inputPath = "assets",
        string $outputPath = "public",
        bool $compress = true,
        bool $embedSourceMap = true,
        bool $addToGitFtpInclude = true
    ) {
        $this->addInitCommand('apk add npm');
        $this->addInitCommand('npm install -g sass');
        $this->addCommand('sass --update ' . $inputPath . ':' . $outputPath . ($compress ? ' --style=compressed' : '') . ($embedSourceMap ? ' --embed-source-map' : ''));
        if ($addToGitFtpInclude) {
            $this->addCommand('echo "!/' . $outputPath . '/" >> .git-ftp-include;');
        }
    }
}
