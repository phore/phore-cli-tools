<?php

namespace App;


use Phore\CliTools\Helper\GetOptResult;
use Phore\CliTools\PhoreAbstractCli;

class TestCli extends PhoreAbstractCli
{

    public function __construct()
    {
        parent::__construct(
            "description of service",
            "",
            ""
        );
    }


    protected function main(array $argv, int $argc, GetOptResult $opts)
    {
        print_r ($argv);
    }
}