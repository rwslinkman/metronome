<?php
namespace Metronome\Injection;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class MetronomeParameterBagArgument extends MetronomeArgument
{
    public function __construct(string $parameterName, array $parameters = [])
    {
        parent::__construct($parameterName, new ParameterBag($parameters));
    }
}