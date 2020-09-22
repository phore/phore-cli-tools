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

    public function __construct(array $result, array $argv)
    {
        $this->result = $result;
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

    public function shift(int $num=1)
    {
        return array_shift($this->argv);
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
