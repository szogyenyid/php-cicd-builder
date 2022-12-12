<?php

namespace szogyenyid\PhpCicdBuilder\Strategies;

use szogyenyid\PhpCicdBuilder\Constants\Trigger;
use szogyenyid\PhpCicdBuilder\Interfaces\ProviderStrategy;

class Bitbucket implements ProviderStrategy
{
    public function pipelineToArray(array &$variables, array &$steps): array
    {
        $pipeline = array();
        foreach ($variables as $var) {
            array_push($pipeline, array("variables" => array($var->toArray($this))));
        }
        foreach ($steps as $step) {
            array_push($pipeline, array("step" => $step->toArray($this)));
        }
        return $pipeline;
    }
    public function stepToArray(string $name, bool $isManual, array &$initScripts, array &$scripts): array
    {
        $step = array();
        $step['name'] = $name;
        if ($isManual) {
            $step['trigger'] = Trigger::MANUAL;
        }
        $step['script'] = array();
        $stepInits = array();
        $apkAdds = array();
        $stepCommands = array();
        foreach ($initScripts as $initScript) {
            foreach ($initScript->getCommands() as $command) {
                if (!in_array($command, $stepInits)) {
                    array_push($stepInits, $command);
                }
            }
            foreach ($initScript->getInitCommands() as $command) {
                array_push($stepInits, $command);
            }
        }
        foreach ($scripts as $script) {
            foreach ($script->getInitCommands() as $initCommand) {
                if (!in_array($initCommand, $stepCommands)) {
                    $matches = array();
                    if (preg_match('/^apk add (.+)/', $initCommand, $matches)) {
                        if (!in_array($matches[1], $apkAdds)) {
                            array_push($apkAdds, $matches[1]);
                        }
                    } else {
                        array_push($stepCommands, $initCommand);
                    }
                }
            }
            foreach ($script->getCommands() as $command) {
                array_push($stepCommands, $command);
            }
        }
        if (!empty($apkAdds)) {
            array_push($step['script'], 'apk add ' . implode(' ', $apkAdds));
        }
        $step['script'] = array_merge(
            $stepInits,
            (!empty($apkAdds) ? array('apk add ' . implode(' ', $apkAdds)) : array()),
            $stepCommands
        );
        return $step;
    }
    public function variablesToArray(string $name, ?string $defaultValue, ?array &$allowedValues): array
    {
        $variables = array();
        $variables['name'] = $name;
        if (!is_null($defaultValue)) {
            $variables['default'] = $defaultValue;
        }
        if (!is_null($allowedValues)) {
            $variables['allowed-values'] = $allowedValues;
        }
        return $variables;
    }
}
