<?php

namespace szogyenyid\PhpCicdBuilder;

use szogyenyid\PhpCicdBuilder\Interfaces\Arrayable;
use szogyenyid\PhpCicdBuilder\Interfaces\ProviderStrategy;

final class Step implements Arrayable
{
    private $name;
    private $scripts = array();
    private $initScripts = array();
    private $isManual = false;
    private $deployment = null;

    public function __construct(string $name = "")
    {
        $this->name = $name;
    }
    public function withDeployment(string $deployment): Step
    {
        $this->deployment = $deployment;
        return $this;
    }
    public function withScript(Script $script): Step
    {
        array_push($this->scripts, $script);
        return $this;
    }
    public function withInitScript(Script $script): Step
    {
        array_push($this->initScripts, $script);
        return $this;
    }
    public function withManualTrigger(bool $manual): Step
    {
        $this->isManual = $manual;
        return $this;
    }
    public function toArray(ProviderStrategy &$strategy): array
    {
        return $strategy->stepToArray(
            $this->name,
            $this->deployment,
            $this->isManual,
            $this->initScripts,
            $this->scripts
        );
    }
}
