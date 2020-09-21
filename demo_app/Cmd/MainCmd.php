<?php


namespace Phore\CliTools\Demo\Cmd;


use Phore\CliTools\Cli\CliContext;
use Phore\CliTools\PhoreAbstractMainCmd;
use Phore\Core\Exception\InvalidDataException;

/**
 * Class MainCmd
 * @package Phore\CliTools\Demo\Cmd
 * @internal
 */
class MainCmd extends PhoreAbstractMainCmd
{

    public function invoke(CliContext $cliContext)
    {
        $opts = $cliContext->getOpts("h");
        if ($opts->has("h")) {
            echo "help";
            $cliContext->printHelpAndExit();
        }

        switch ($opts->argv(0)) {
            case "user":
                return $cliContext->dispatch(new UserCmd());

            case null:
                throw new \InvalidArgumentException("Missing command.");

            default:
                throw new InvalidDataException("Invalid command '{$opts->argv(0)}'");
        }
    }
}