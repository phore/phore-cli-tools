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
    private $argv;
    private $cmd;

    public function __construct(string $cmd = null, array $result, array $argv)
    {
        $this->result = $result;
        $this->argv = $argv;
        $this->cmd = $cmd;
    }

    public function has(string $key) : bool
    {
        return isset ($this->result[$key]);
    }

    public function getCmd() : ?string
    {
        return $this->cmd;
    }

    public function hasCmd() : bool
    {
        return $this->cmd !== null;
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
     * @return array|string|null
     */
    public function argv(int $index=null, $default=null)
    {
        print_r ($this);
        if ($index !== null) {
            if (isset ($this->argv[$index]))
                return $this->argv[$index];
            if ($default instanceof \Exception)
                throw $default;
            return $default;
        }
        return $this->argv;
    }
}
