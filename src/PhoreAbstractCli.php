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

    public function run()
    {
        $argv = $GLOBALS["argv"];
        $argc = $GLOBALS["argc"];
        $this->cmdName = $argv[0];
        $_getOpt = getopt($this->options, $this->longOpts, $optInd);
        $opts = new GetOptResult($_getOpt);

        $opts = $this->opts = phore_getopt("hv:" . $this->options, $this->longOpts, $optInd);

        if ($opts->has("h") || $opts->has("help")) {
            $this->printHelp();
            exit(2);
        }

        if ($opts->has("v") || $opts->has("verbose")) {
            if (class_exists("Phore\Log\PhoreLogger")) {
                $this->log = new PhoreLogger(new PhoreEchoLoggerDriver());
                $this->log->setLogLevel(LogLevel::DEBUG);
            }
        }

        $argc -= $optInd;
        for ($i = 0; $i<$optInd; $i++)
            array_shift($argv);

        try {
            $this->main($argv, $argc, $opts);
        } catch (\Exception $e) {
            $this->log->emergency($e->getMessage());
            exit(1);
        }
    }

}