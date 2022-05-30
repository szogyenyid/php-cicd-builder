<?php

namespace Soegaeni\PhpCicdBuilder;

final class Pipeline
{
    private $name;
    private $steps = array();

    public function __construct(string $name = "")
    {
        $this->name = $name;
    }
    public function withStep(Step $step): Pipeline
    {
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
    public function asArray(): array
    {
        $pipeline = array();
        foreach ($this->steps as $step) {
            array_push($pipeline, array("step" => $step->asArray()));
        }
        return $pipeline;
    }
}
