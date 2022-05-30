<?php

namespace Soegaeni\PhpCicdBuilder;

class Script
{
    protected $initCommands = array();
    protected $commands = array();

    public function addInitCommand(string $command): Script
    {
        $singleLine = trim(preg_replace('/\s+/', ' ', $command));
        array_push($this->initCommands, $singleLine);
        return $this;
    }
    public function addCommand(string $command): Script
    {
        $singleLine = trim(preg_replace('/\s+/', ' ', $command));
        array_push($this->commands, $singleLine);
        return $this;
    }
    public function getInitCommands(): array
    {
        return $this->initCommands;
    }
    public function getCommands(): array
    {
        return $this->commands;
    }
    public static function simple(string $command): Script
    {
        $script = new Script();
        $script->addCommand($command);
        return $script;
    }
}
