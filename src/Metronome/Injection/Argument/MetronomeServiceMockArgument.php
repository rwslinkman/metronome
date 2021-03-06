<?php
namespace Metronome\Injection\Argument;

class MetronomeServiceMockArgument extends MetronomeMockArgument
{
    public function __construct(string $parameterName, ServiceInjector $injector)
    {
        parent::__construct($parameterName, $injector->serviceClass(), $injector->inject());
    }
}