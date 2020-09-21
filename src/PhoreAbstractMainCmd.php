<?php


namespace Phore\CliTools;


use Phore\CliTools\Cli\ShellCliContext;
use Phore\CliTools\Ex\CliExitException;

abstract class PhoreAbstractMainCmd extends PhoreAbstractCmd
{


    public function main(array $argv, int $argc) : int
    {
        try {
            array_shift($argv);
            $context = new ShellCliContext($argv);
            $this->invoke($context);
        } catch (CliExitException $e) {
            return $e->getCode();
        }
        return 0;
    }
}