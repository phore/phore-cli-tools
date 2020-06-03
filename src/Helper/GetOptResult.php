<?php


namespace Phore\CliTools\Helper;

/**
 * Class GetOptResult
 * @package Phore\Core\Helper
 * @internal
 */
class GetOptResult
{
    private $result;
    private $optind;

    private $argv;

    public function __construct(array $result, $optind)
    {
        $this->result = $result;
        $this->optind = $optind;
        $argv = $GLOBALS["argv"];
        for($i = 0; $i < $optind; $i++)
            array_shift($argv);
        $this->argv = $argv;
    }

    public function has(string $key) : bool
    {
        return isset ($this->result[$key]);
    }


    public function get(string $key, $default=null) : ?string
    {
        if ( ! $this->has($key)) {
            if ($default instanceof \Exception)
                throw $default;
            return $default;
        }
        return $this->result[$key];
    }

    /**
     * Transform the parameter into an absolute path
     *
     *
     *
     * @param string $key
     * @param null $default
     * @return string
     * @throws \Exception
     */
    public function getPathAbs (string $key, $default=null) : string
    {
        $val = $this->get($key, $default);
        if ( ! startsWith($val, "/"))
            $val = getcwd() . "/" . $val;
        return $val;
    }

    public function getArr(string $key, $default=null) : ?array
    {
        if ( ! $this->has($key)) {
            if ($default instanceof \Exception)
                throw $default;
            return $default;
        }
        $result = $this->result[$key];
        if ( ! is_array($result))
            return [ $result ];
        return $result;
    }

    /**
     * Argv array without the parsed
     * elements
     *
     * @return array
     */
    public function argv() : array
    {
        return $this->argv;
    }
}
