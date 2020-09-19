<?php


namespace Phore\CliTools;


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
     * @var LoggerInterface
     */
    public $log;

    /**
     * @var GetOptResult
     */
    public $opts;

    private $longOpts;
    private $options;

    private $commandTitle;
    private $helpFile;
    private $cmdName;

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
    public function __construct(
        string $commandTitle,
        string $helpFile,
        string $options,
        array $longOpts=[]
    )
    {
        $this->log = new NullLogger();
        $this->options = $options;
        $this->longOpts = $longOpts;

        $this->commandTitle = $commandTitle;
        $this->helpFile = $helpFile;
    }

    /**
     * 1= normal;
     * 0=silent mode: only show emergency (-s)
     * 2=debug mode: show also debug messages (-v)
     * @var int
     */
    public $outMode = 1;

    /**
     * Print the help text (defined in constructor)
     *
     * Uses the text-file to generate the help
     * if user runs <command> -h or <command> --help
     *
     */
    protected function printHelp()
    {
        $o  = "{$this->cmdName} - {$this->commandTitle}" . PHP_EOL;
        $o .= "Usage:" . PHP_EOL;
        $o .= "  $this->cmdName [parameters] [command]" . PHP_EOL . PHP_EOL;
        $o .= "Parameters:" . PHP_EOL;
        $o .= "  -v --verbose       Be verbose (output debug info)" . PHP_EOL;
        $o .= "  -s --silent        Output only errors" . PHP_EOL;
        $o .= "  -h --help          Show this help" . PHP_EOL;

        $o .= file_get_contents($this->helpFile);

        $this->out($o . PHP_EOL);
    }



    /**
     * Execute depending
     *
     * ```
     * $this->execMap([
     *      "createThing" => function(array $argv, string $carg) {},
     *      "deleteThing" => function(array $argv, int $argc, string $carg) {[
     * ]);
     * ```
     *
     * @param array $map
     * @throws UserInputException
     */
    protected function execMap(array $map)
    {
        $argv = $this->opts->argv();
        $nextArg = array_shift($argv);

        if ($nextArg === null)
            throw new UserInputException("Missing command");

        if ( ! isset ($map[$nextArg]))
            throw new UserInputException("Operation '$nextArg' unknown");

        if ( ! is_callable($map[$nextArg]))
            throw new \InvalidArgumentException("Operation '$nextArg' points to non callable");

        // Call the function
        ($map[$nextArg])($argv, count ($argv), $nextArg);
    }

    /**
     * Main function called by the cli executable.
     *
     * It will run self::main() with expected parameters
     *
     * Default Options will apply
     *      -h --help       Show help
     *      -v --verbose    Be verbose
     *
     *
     */
    public function run(array $argv = null)
    {
        if ($argv === null)
            $argv = $GLOBALS["argv"];
        $this->cmdName = array_shift($argv);

        // Generate the opts Object with options and the number of parsed options

        $getOpsParser = new GetOptParser($this->options, $this->longOpts);
        $opts = $getOpsParser->getOpts($argv);

        // Print help if -h or --help
        if ($opts->has("h") || $opts->has("help")) {
            $this->printHelp();
            exit(2);
        }



        // Run the main function and transform exceptions to CLI printable
        // Results

    }

    /**
     * Your code executed here
     *
     * @param array $argv
     * @param int $argc
     * @param GetOptResult $opts
     * @return mixed
     */
    abstract protected function main(GetOptResult $opts);

}