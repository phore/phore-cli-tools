<?php


namespace Phore\CliTools;


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
    protected $log;

    protected $opts;

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


    abstract protected function main(array $argv, int $argc, GetOptResult $opts);

    /**
     * Main function called by the cli executable.
     *
     * It will run self::main() with expected parameters
     *
     *
     */
    public function run()
    {
        $argv = $GLOBALS["argv"];
        $argc = $GLOBALS["argc"];
        $this->cmdName = $argv[0];

        // Generate the opts Object with options and the number of parsed options
        $_getOptRes = getopt($this->options, $this->longOpts, $optInd);
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
            $this->main($argv, $argc, $opts);
        } catch (\Exception $e) {
            $this->log->emergency($e->getMessage());
            exit(1);
        }
    }

}