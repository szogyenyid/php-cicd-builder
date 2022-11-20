<?php

namespace Soegaeni\PhpCicdBuilder;

use Soegaeni\PhpCicdBuilder\Interfaces\ProviderStrategy;
use Soegaeni\PhpCicdBuilder\Script;
use Symfony\Component\Yaml\Yaml;

final class CICD
{
    private string $image;
    private ProviderStrategy $providerStrategy;
    private array $pipelines = array();
    private array $initScripts = array();
    private array $finalYmlArray = array();

    public function __construct(string $dockerImage, ProviderStrategy $providerStrategy)
    {
        $this->image = $dockerImage;
        $this->providerStrategy = $providerStrategy;
    }
    public function withPipeline(string $trigger, Pipeline &$pipeline): CICD
    {
        if (!isset($this->pipelines[$trigger])) {
            $this->pipelines[$trigger] = array();
        }
        foreach ($pipeline->getSteps() as &$step) {
            foreach ($this->initScripts as $is) {
                $step->withInitScript($is);
            }
        }
        $this->pipelines[$trigger][$pipeline->getName()] = $pipeline->toArray($this->providerStrategy);
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
    public function writeToFile(string $path): void
    {
        $this->finalYmlArray['image'] = $this->image;
        $this->finalYmlArray['pipelines'] = $this->pipelines;
        $yml = Yaml::dump($this->finalYmlArray, 20, 2);
        // Remove unnecessary newlines
        $yml = preg_replace('/\-\n\s+/', '- ', $yml);
        $yml = preg_replace("/- '(.+?)'/", "- $1", $yml);
        file_put_contents($path, $yml);
        return;
    }
}
