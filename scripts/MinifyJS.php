<?php

namespace MyProject\CICD;

use szogyenyid\PhpCicdBuilder\Script;

class MinifyJS extends Script
{
    public function __construct(
        string $inputFolder = "assets",
        string $outputFolder = null,
        bool $addToGitFtpInclude = true,
        bool $debug = false
    ) {
        $this->addInitCommand("apk add nodejs");
        $this->addInitCommand("apk add npm");
        if (isset($outputFolder)) {
            $this->addInitCommand('npm install uglifyjs-folder -g');
            $this->addCommand('uglifyjs-folder ' . $inputFolder . ' -eo ' . $outputFolder);
            if ($addToGitFtpInclude) {
                $this->addCommand('echo "' . $outputFolder . '/*" >> .git-ftp-include');
            }
        } else {
            $this->addInitCommand("npm install uglify-js -g");
            $this->addCommand('
                for f in $(find ' . $inputFolder . ' -maxdepth 30 -type f -name \'*.js\' -and -not -name \'*.min.js\');
                do
                    to=".min.js";
                    filename=${f/.js/$to};
                    uglifyjs --compress --mangle -- "$f" > $filename;
                    ' . ($addToGitFtpInclude ? 'echo "!${filename}" >> .git-ftp-include;' : '') . '
                done
            ');
        }
        if ($debug) {
            $this->addCommand('git status -uno --porcelain');
            $this->addCommand('cat .git-ftp-include');
        }
    }
}
