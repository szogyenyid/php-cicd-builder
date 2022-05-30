<?php

namespace Soegaeni\PhpCicdBuilder;

final class Step
{
    private $name;
    private $scripts = array();
    private $initScripts = array();

    public function __construct(string $name = "")
    {
        $this->name = $name;
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
    public function asArray(): array
    {
        $step = array();
        $step['name'] = $this->name;
        $step['script'] = array();
        $stepInits = array();
        $apkAdds = array();
        $stepCommands = array();
        foreach ($this->initScripts as $initScript) {
            foreach ($initScript->getCommands() as $command) {
                if (!in_array($command, $stepInits)) {
                    array_push($stepInits, $command);
                }
            }
            foreach ($initScript->getInitCommands() as $command) {
                array_push($stepInits, $command);
            }
        }
        foreach ($this->scripts as $script) {
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
}
