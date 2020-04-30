<?php


namespace Phore\Cli;


class AbstractCli
{

    protected $log;

    public function __construct()
    {
        $this->log = new NullLogger();
    }

    protected function printHelp()
    {

    }



}