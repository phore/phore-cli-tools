<?php


namespace Phore\CliTools\Ex;


use Throwable;

class CliExitException extends \Exception
{
    public function __construct($exit_code = 0, $message = "", Throwable $previous = null)
    {
        parent::__construct($message, $exit_code, $previous);
    }
}