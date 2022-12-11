<?php

namespace szogyenyid\PhpCicdBuilder;

use szogyenyid\PhpCicdBuilder\Interfaces\Arrayable;
use szogyenyid\PhpCicdBuilder\Interfaces\ProviderStrategy;

final class Pipeline implements Arrayable
{
    private $name;
    private $steps = array();
    private $variables = array();

    public function __construct(string $name = "")
    {
        $this->name = $name;
    }
    public function withVariables(Variables $variables): Pipeline
    {
        array_push($this->variables, $variables);
        return $this;
    }
    public function withStep(Step $step): Pipeline
    {
        array_push($this->steps, $step);
        return $this;
    }
    public function withManualStep(Step $step): Pipeline
    {
        $step = $step->withManualTrigger(true);
        array_push($this->steps, $step);
        return $this;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getSteps(): array
    {
        return $this->steps;
    }
    public function toArray(ProviderStrategy &$strategy): array
    {
        return $strategy->pipelineToArray($this->variables, $this->steps);
    }
}
