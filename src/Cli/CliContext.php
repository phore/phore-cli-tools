<?php


namespace Phore\CliTools\Cli;


use Phore\CliTools\Helper\GetOptParser;
use Phore\CliTools\Helper\GetOptResult;
use Phore\CliTools\PhoreAbstractCmd;
use Phore\Core\Exception\InvalidDataException;

interface CliContext
{
    public function __construct(array $argv = []);

    /**
     * Emergency = 0
     * Notice = 5
     * Debug = 7
     *
     * @param int $verbosity
     */
    public function setVerbosity(int $verbosity);

    /**
     * Output a message to stdout
     *
     * Output is "regular output". Unless --silent is specified, this will
     * generate output
     *
     * @param mixed ...$msg
     */
    public function out(...$msg);

    /**
     * Output debug-message to stdout
     *
     * Output send with this function is only outputted if --verbose
     * is specified
     *
     * @param mixed ...$msg
     */
    public function debug(...$msg);

    public function stdout($data);

    /**
     * Output emergency message (with red border)
     *
     * Output of this method will be outputted everytime and is fomatted red.
     *
     * @param mixed ...$msg
     */
    public function emergency(...$msg);

    public function askYesNo(string $question, bool $default=null);

    /**
     * @param string $question
     * @param null $default
     * @param callable|string|null $validator   Regex or callback to validate input
     * @return mixed|string
     * @throws InvalidDataException
     */
    public function ask(string $question, $default=null, $validator=null);


    /**
     * @param string $shortOpts
     * @param array $longOpts
     * @param array|null $argv
     * @return self
     */
    public function cloneWithSubOpts(string $shortOpts="", array $longOpts=[], array $argv = null);


    public function parseOpts(array $argv);

    public function getOpts(string $shortOpts="", array $longOpts=[]) : GetOptResult;

    public function getEnv(string $envName=null, $default=null);

    public function printHelpAndExit(string $helpfile=null);

    /**
     * Execute a subcommand with a clone of this
     * context adjusted to parsed getOpts() result
     *
     * @param PhoreAbstractCmd $cmd
     * @return mixed
     */
    public function dispatch(PhoreAbstractCmd $cmd);
    public function dispatchMap(array $cmdMap, GetOptResult $getOptResult) : ?int;

    public function exit($code=0, $messge=null);

}