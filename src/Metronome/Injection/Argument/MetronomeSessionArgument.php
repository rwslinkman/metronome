<?php
namespace Metronome\Injection\Argument;

class MetronomeSessionArgument extends MetronomeServiceArgument
{
    const SESSION_SERVICE_ID = "session";

    public function __construct(string $parameterName)
    {
        parent::__construct($parameterName, self::SESSION_SERVICE_ID);
    }
}