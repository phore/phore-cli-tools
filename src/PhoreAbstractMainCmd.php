<?php


namespace Phore\CliTools;


use Phore\CliTools\Ex\UserInputException;
use Phore\CliTools\Helper\ColorOutput;
use Psr\Log\LogLevel;

abstract class PhoreAbstractMainCmd extends PhoreAbstractCmd
{

    public function run($argv=null)
    {
// Register Verbose logger on -v or --verbose
        if ($opts->has("v") || $opts->has("verbose")) {
            $this->outMode = 2;
            if (class_exists("Phore\Log\PhoreLogger")) {
                $this->log = new PhoreLogger(new PhoreEchoLoggerDriver());
                $this->log->setLogLevel(LogLevel::DEBUG);
            }
        }

        if ($opts->has("s") || $opts->has("silent")) {
            $this->outMode = 0; // Show only emergency()
        }

        try {
            $this->main($opts->argv(), count($opts->argv()), $opts);
        } catch (UserInputException $e) {
            $this->emergency(  ColorOutput::Str($e->getMessage(), "red"). PHP_EOL);
            $this->emergency(  "Run '$this->cmdName -h' to see help". PHP_EOL);

            if ($this->outMode === 2)
                throw $e; // Throw exception to main if debug mode (see backtrace)
            exit(1);
        } catch (\Exception $e) {
            $this->emergency(ColorOutput::Str("Error: " . $e->getMessage(), "red"). PHP_EOL);
            $this->emergency("Set parameter '--verbose' to see the backtrace" . PHP_EOL);
            if ($this->outMode === 2)
                throw $e; // Throw exception to main if debug mode (see backtrace)
            exit(1);
        }
    }

}