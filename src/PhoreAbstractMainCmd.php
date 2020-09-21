<?php


namespace Phore\CliTools;


use Phore\CliTools\Cli\ShellCliContext;
use Phore\CliTools\Ex\CliExitException;
use Phore\CliTools\Ex\UserInputException;

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
        } catch (UserInputException $e) {
            $context->debug($e->getTraceAsString());
            $context->emergency($e->getMessage());
            return 254;
        }
        return 0;
    }
}