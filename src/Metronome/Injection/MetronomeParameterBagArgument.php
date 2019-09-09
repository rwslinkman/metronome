<?php
namespace Metronome\Injection;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class MetronomeParameterBagArgument extends MetronomeArgument
{
    public function __construct(string $parameterName)
    {
        parent::__construct($parameterName, new ParameterBag());
    }
}