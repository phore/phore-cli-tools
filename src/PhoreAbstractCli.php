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

    protected function printHelp()
    {
        $o  = "{$this->cmdName} - {$this->commandTitle}" . PHP_EOL;
        $o .= "Usage:" . PHP_EOL;

        file_put_contents("php://stdout", $o);
    }


    /**
     * Output a message
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

    abstract protected function main(array $argv, int $argc, GetOptResult $opts);


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

        if ( ! isset ($map[$nextArg]))
            throw new UserInputException("Operation '$nextArg' unknown.");

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
        } catch (\Exception $e) {
            $this->outEmergency("Error: " . $e->getMessage() . PHP_EOL);
            exit(1);
        }
    }

}