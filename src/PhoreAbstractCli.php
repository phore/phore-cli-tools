<?php


namespace Phore\CliTools;


use Phore\CliTools\Ex\UserInputException;
use Phore\CliTools\Helper\ColorOutput;
use Phore\CliTools\Helper\GetOptResult;
use Phore\Log\Logger\PhoreEchoLoggerDriver;
use Phore\Log\PhoreLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

abstract class PhoreAbstractCli
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

        $o .= file_get_contents($this->helpFile);

        $this->out($o . PHP_EOL);
    }

    /**
     * Output a message to stdout
     *
     * @param mixed ...$msg
     */
    public function out(...$msg)
    {
        file_put_contents("php://stderr", implode(" ", $msg));
    }

    /**
     * Output emergency message (with red border)
     *
     * @param mixed ...$msg
     */
    public function outEmergency(...$msg)
    {
        file_put_contents(
            "php://stderr",
            ColorOutput::Str(implode(" ", $msg), "red")
        );
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
    public function run()
    {
        $argv = $GLOBALS["argv"];
        $argc = $GLOBALS["argc"];
        $this->cmdName = $argv[0];

        // Generate the opts Object with options and the number of parsed options
        $_getOptRes = getopt("hv" . $this->options, $this->longOpts, $optInd);
        $opts = $this->opts = new GetOptResult($_getOptRes, $optInd);


        // Print help if -h or --help
        if ($opts->has("h") || $opts->has("help")) {
            $this->printHelp();
            exit(2);
        }

        // Register Verbose logger on -v or --verbose
        if ($opts->has("v") || $opts->has("verbose")) {
            if (class_exists("Phore\Log\PhoreLogger")) {
                $this->log = new PhoreLogger(new PhoreEchoLoggerDriver());
                $this->log->setLogLevel(LogLevel::DEBUG);
            }
        }

        // Run the main function and transform exceptions to CLI printable
        // Results
        try {
            $this->main($opts->argv(), count($opts->argv()), $opts);
        } catch (UserInputException $e) {
            $this->outEmergency(  $e->getMessage(). PHP_EOL);
            $this->outEmergency(  "Run '$this->cmdName -h' to see help". PHP_EOL);
            exit(1);
        } catch (\Exception $e) {
            $this->outEmergency("Error: " . $e->getMessage() . PHP_EOL);
            exit(1);
        }
    }

    /**
     * Your code executed here
     *
     * @param array $argv
     * @param int $argc
     * @param GetOptResult $opts
     * @return mixed
     */
    abstract protected function main(array $argv, int $argc, GetOptResult $opts);

}