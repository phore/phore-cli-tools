<?php


namespace Phore\CliTools\Helper;


use Phore\Core\Exception\InvalidDataException;

class OutputHandler
{
    /**
     * Output a message to stdout
     *
     * Output is "regular output". Unless --silent is specified, this will
     * generate output
     *
     * @param mixed ...$msg
     */
    public function out(...$msg)
    {
        if ($this->outMode < 1)
            return;
        file_put_contents("php://stderr", implode(" ", $msg));
    }


    public function askYesNo(string $question, bool $default=null)
    {
        while (true) {
            $result = readline($question);
            if ($result === false)
                throw new InvalidDataException("Invalid answer to question '$question'.");


        }
    }

    /**
     * @param string $question
     * @param null $default
     * @param callable|string|null $validator   Regex or callback to validate input
     * @return mixed|string
     * @throws InvalidDataException
     */
    public function ask(string $question, $default=null, $validator=null)
    {
        while (true) {
            $answer = readline($question);
            if ($answer === false)
                throw new InvalidDataException("Invalid answer to question '$question'.");

            if ($default !== null && $answer === "")
                return $default;

            // Validate only non-default answers
            if ($validator !== null) {
                if (is_string($validator)) {
                    if (! preg_match ($validator, $answer)) {
                        $this->out("Invalid input: ($validator)\n");
                        continue;
                    }

                }

                if (is_callable($validator)) {
                    $validatedAnswer = $validator($answer);
                    if ($validatedAnswer === null)
                        continue; // invalid
                    return $validatedAnswer;
                }

            }
            return $answer;
        }
    }

    /**
     * Output debug-message to stdout
     *
     * Output send with this function is only outputted if --verbose
     * is specified
     *
     * @param mixed ...$msg
     */
    public function debug(...$msg)
    {
        if ($this->outMode < 2)
            return;
        file_put_contents("php://stderr", implode(" ", $msg));
    }


    /**
     * Output emergency message (with red border)
     *
     * Output of this method will be outputted everytime and is fomatted red.
     *
     * @param mixed ...$msg
     */
    public function emergency(...$msg)
    {
        file_put_contents(
            "php://stderr",
            implode(" ", $msg)
        );
    }
}