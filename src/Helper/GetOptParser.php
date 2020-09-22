<?php


namespace Phore\CliTools\Helper;


use Phore\CliTools\Ex\UserInputException;

class GetOptParser
{

    const PARAM_BOOL = "bool";
    const PARAM_REQ = "req";
    const PARAM_OPT = "opt";

    private $shortOpts = [];
    private $longOpts = [];


    public function __construct(string $shortopts, array $longopts)
    {
        $len = strlen($shortopts);
        for ($i=0; $i<$len; $i++) {
            $char = $shortopts[$i];
            if (substr($shortopts, $i+1, 2) === "::") {
                $i += 2;
                $this->shortOpts[$char] = self::PARAM_OPT;
                continue;
            }
            if (substr($shortopts, $i+1, 1) === ":") {
                $i += 1;
                $this->shortOpts[$char] = self::PARAM_REQ;
                continue;
            }
            $this->shortOpts[$char] = self::PARAM_BOOL;
        }

        foreach ($longopts as $opt) {
            if (endsWith($opt, "::")) {
                $this->longOpts[substr($opt, 0, -2)] = self::PARAM_OPT;
                continue;
            }
            if (endsWith($opt, ":")) {
                $this->longOpts[substr($opt, 0, -1)] = self::PARAM_REQ;
                continue;
            }
            $this->longOpts[$opt] = self::PARAM_BOOL;
        }
    }

    

    public function getOpts(array $argv, array &$rest=null) : GetOptResult
    {
        
        $parsedOpts = [];
        while (count($argv) > 0) {
            $curArg = $argv[0];
            if (startsWith($curArg, "--")) {
                $p = substr($curArg, 2);
                if ( ! isset($this->longOpts[$p]))
                    throw new UserInputException("Unrecognized option: '--$p'");
                $type = $this->longOpts[$p];
            } else if (startsWith($curArg, "-")) {
                $p = substr($curArg, 1);
                if ( ! isset($this->shortOpts[$p]))
                    throw new UserInputException("Unrecognized option: '--$p'");
                $type = $this->shortOpts[$p];
            } else {

                $rest = $argv;
                return new GetOptResult($parsedOpts, $argv);
            }
            array_shift($argv);
            if ($type === self::PARAM_BOOL)
                $parsedOpts[$p] = true;
            if ($type === self::PARAM_REQ) {
                if (count($argv) === 0)
                    throw new UserInputException("Missing value for parameter '$curArg'");
                $value = array_shift($argv);
                $parsedOpts[$p] = $value;
            }
        }
        $rest = $argv;
        return new GetOptResult($parsedOpts, $argv);
    }
}