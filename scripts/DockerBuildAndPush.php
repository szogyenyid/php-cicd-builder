<?php

namespace szogyenyid\PhpCicdBuilder\ExampleScripts;

use szogyenyid\PhpCicdBuilder\Script;

class DockerBuildAndPush extends Script
{
    public function __construct(string $userName, string $password, string $image, string $repo)
    {
        $this->addInitCommand("apk add docker");
        $this->addCommand('docker build -t ' . $image . ' .');
        $this->addCommand('docker login --username ' . $userName . ' --password ' . $password);
        $this->addCommand('docker tag ' . $image . ' ' . $repo);
        $this->addCommand('docker push ' . $repo);
    }
}
