<?php

namespace Soegaeni\PhpCicdBuilder;

use Soegaeni\PhpCicdBuilder\Script;
use Symfony\Component\Yaml\Yaml;

final class CICD
{
    private $image;
    private $pipelines = array();
    private $initScripts = array();
    private $finalYmlArray = array();

    public function __construct(string $dockerImage)
    {
        $this->image = $dockerImage;
    }
    public function withPipeline(string $trigger, Pipeline $pipeline): CICD
    {
        if (!isset($this->pipelines[$trigger])) {
            $this->pipelines[$trigger] = array();
        }
        foreach ($pipeline->getSteps() as &$step) {
            foreach ($this->initScripts as $is) {
                $step->withInitScript($is);
            }
        }
        $this->pipelines[$trigger][$pipeline->getName()] = $pipeline->asArray();
        return $this;
    }
    public function withPipelines(string $trigger, ...$pipelines): CICD
    {
        foreach ($pipelines as $pl) {
            if ($pl instanceof Pipeline) {
                $this->withPipeline($trigger, $pl);
            } else {
                throw new \Exception("CICD::withPipelines parameters must be Pipeline instances.");
            }
        }
        return $this;
    }
    public function withInitScript(Script $script): CICD
    {
        $this->initScripts[] = $script;
        return $this;
    }
    private function prepareArray()
    {
        $this->finalYmlArray['image'] = $this->image;
        $this->finalYmlArray['pipelines'] = $this->pipelines;
    }
    public function writeToFile(string $path): void
    {
        $this->prepareArray();
        $yml = Yaml::dump($this->finalYmlArray, 10, 2);
        // Remove unnecessary newlines
        $yml = preg_replace('/\-\n\s+/', '- ', $yml);
        $yml = preg_replace("/- '(.+?)'/", "- $1", $yml);
        file_put_contents($path, $yml);
        return;
    }
}
