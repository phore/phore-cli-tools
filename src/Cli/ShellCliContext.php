<?php


namespace Phore\CliTools\Cli;


use Phore\CliTools\Ex\CliExitException;
use Phore\CliTools\Helper\GetOptParser;
use Phore\CliTools\Helper\GetOptResult;
use Phore\CliTools\PhoreAbstractCmd;
use Phore\Core\Exception\InvalidDataException;

class ShellCliContext implements CliContext
{

    public function __construct(array $argv = [])
    {
        $this->argv = $this->unparsedArgv = $argv;
    }



    private $verbosity = 7;

    /**
     * @var array
     */
    private $argv;

    /**
     * @var array
     */
    private $unparsedArgv;

    /**
     * Emergency = 0
     * Notice = 5
     * Debug = 7
     *
     * @param int $verbosity
     */
    public function setVerbosity(int $verbosity)
    {
        $this->verbosity = $verbosity;
    }

    /**
     * Output a message to stdout
     *
     * Output is "regular output". Unless --silent is specified, this will
     * generate output
     *
     * @param mixed ...$msg
     */
    public function out(...$msg)
    {
        if ($this->verbosity < 5)
            return;
        file_put_contents("php://stderr", implode(" ", $msg));
    }
    /**
     * Output debug-message to stdout
     *
     * Output send with this function is only outputted if --verbose
     * is specified
     *
     * @param mixed ...$msg
     */
    public function debug(...$msg)
    {
        if ($this->verbosity < 7)
            return;
        file_put_contents("php://stderr", implode(" ", $msg));
    }


    /**
     * Output emergency message (with red border)
     *
     * Output of this method will be outputted everytime and is fomatted red.
     *
     * @param mixed ...$msg
     */
    public function emergency(...$msg)
    {
        if ($this->verbosity < 0)
            return;
        file_put_contents(
            "php://stderr",
            implode(" ", $msg)
        );
    }

    public function askYesNo(string $question, bool $default=null)
    {
        while (true) {
            $result = readline($question);
            if ($result === false)
                throw new InvalidDataException("Invalid answer to question '$question'.");
        }
    }

    /**
     * @param string $question
     * @param null $default
     * @param callable|string|null $validator   Regex or callback to validate input
     * @return mixed|string
     * @throws InvalidDataException
     */
    public function ask(string $question, $default=null, $validator=null)
    {
        while (true) {
            $answer = readline($question);
            if ($answer === false)
                throw new InvalidDataException("Invalid answer to question '$question'.");

            if ($default !== null && $answer === "")
                return $default;

            // Validate only non-default answers
            if ($validator !== null) {
                if (is_string($validator)) {
                    if (! preg_match ($validator, $answer)) {
                        $this->out("Invalid input: ($validator)\n");
                        continue;
                    }

                }

                if (is_callable($validator)) {
                    $validatedAnswer = $validator($answer);
                    if ($validatedAnswer === null)
                        continue; // invalid
                    return $validatedAnswer;
                }

            }
            return $answer;
        }
    }


    protected function printHelp(string $helpfile=null)
    {
        $o  = "" . PHP_EOL;
        $o .= "Usage:" . PHP_EOL;
        $o .= "   [parameters] [command]" . PHP_EOL . PHP_EOL;
        $o .= "Parameters:" . PHP_EOL;
        $o .= "  -v --verbose       Be verbose (output debug info)" . PHP_EOL;
        $o .= "  -s --silent        Output only errors" . PHP_EOL;
        $o .= "  -h --help          Show this help" . PHP_EOL;

        if ($helpfile !== null)
            $o .= file_get_contents($helpfile);

        $this->out($o . PHP_EOL);
    }

    public function cloneWithSubOpts(string $shortOpts = "", array $longOpts = [], array $argv = null): self
    {
        $new = clone $this;
        $getOptsParser = new GetOptParser($shortOpts, $longOpts);
        $new->opts = $getOptsParser->getOpts($argv);
        return $new;
    }

    public function parseOpts(array $argv)
    {
        // TODO: Implement parseOpts() method.
    }

    public function getOpts(string $shortOpts=null, array $longOpts=[]): GetOptResult
    {
        $getOptsParser = new GetOptParser($shortOpts, $longOpts);
        return $getOptsParser->getOpts($this->argv, $this->unparsedArgv);
    }

    public function getEnv(string $envName=null, $default=null)
    {
        $env = getenv($envName);
        if ($env === false)
            return $default;
        return $env;
    }

    public function printHelpAndExit(string $helpfile=null)
    {
        $this->out("Help");
        $this->exit(0);
    }

    public function exit($code = 0, $messge = null)
    {
        throw new CliExitException($code, $messge);
    }


    public function dispatch(PhoreAbstractCmd $cmd)
    {
        $newContextInstance = clone($this);
        $newContextInstance->argv = $newContextInstance->unparsedArgv;

        return $cmd->invoke($newContextInstance);
    }
}