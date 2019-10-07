<?php
namespace Metronome\Injection\Argument;

class MetronomeMockArgument extends MetronomeArgument
{
    public function __construct(string $parameterName, $className, $injection)
    {
        parent::__construct($parameterName, MockCreator::mock($className, $injection));
    }
}