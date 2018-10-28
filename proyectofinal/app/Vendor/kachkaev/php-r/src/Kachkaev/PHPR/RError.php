<?php
namespace Kachkaev\PHPR;

/**
 * Used to store R error message and its context
 * (input command, command number and line number)
 * 
 * @author  "Alexander Kachkaev <alexander@kachkaev.ru>"
 */
class RError
{
    private $inputLineNumber;
    private $commandNumber;
    private $command;
    private $errorMessage;

    public function __construct($inputLineNumber, $commandNumber, $command, $errorMessage)
    {
        $this->inputLineNumber = $inputLineNumber;
        $this->commandNumber = $commandNumber;
        $this->command = $command;
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return int zero-based R command line number that caused the error 
     */
    public function getInputLineNumber()
    {
        return $this->inputLineNumber;
    }

    /**
     * @return int zero-based R command number that caused the error 
     */
    public function getCommandNumber()
    {
        return $this->commandNumber;
    }

    /**
     * @return string returns the R command the caused the error
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return string message generated by R
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
