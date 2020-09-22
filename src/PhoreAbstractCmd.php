<?php


namespace Phore\CliTools;


use Phore\CliTools\Cli\CliContext;
use Phore\CliTools\Ex\UserInputException;
use Phore\CliTools\Helper\ColorOutput;
use Phore\CliTools\Helper\GetOptParser;
use Phore\CliTools\Helper\GetOptResult;
use Phore\Core\Exception\InvalidDataException;
use Phore\Log\Logger\PhoreEchoLoggerDriver;
use Phore\Log\PhoreLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

abstract class PhoreAbstractCmd
{


    /**
     * PhoreAbstractCli constructor.
     *
     * Define the basic configuration of the cli
     * in the child class.
     *
     * ```
     * public function __construct()
     * {
     *     parent::__construct(
     *         "IaC service tool",
     *         __DIR__ . "/../cli_help.txt",
     *         "p:m",
     *         ["file"]
     *      );
     * );
     *
     * ```
     *
     * @param string $commandTitle  The title to display in help
     * @param string $helpFile      The txt file to output if -h or --help is parameter
     * @param string $options       Options string following the getopt() rules
     * @param array $longOpts       Options array following the getopt() rules
     */
    public function __construct()
    {
    }






    

    /**
     * Your code executed here
     *
     * @param array $argv
     * @param int $argc
     * @param GetOptResult $opts
     * @return mixed
     */
    abstract public function invoke(CliContext $context);

}