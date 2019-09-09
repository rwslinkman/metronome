<?php
namespace Metronome\Injection;

class MetronomeArgument
{
    /** @var mixed */
    private $injectedArgument;
    /** @var string */
    private $parameterName;

    /**
     * MetronomeArgument constructor.
     * @param string $parameterName
     * @param mixed $injectedArgument
     */
    public function __construct(string $parameterName, $injectedArgument)
    {
        $this->parameterName = $parameterName;
        $this->injectedArgument = $injectedArgument;
    }

    /**
     * @return string
     */
    public function getParameterName(): string
    {
        return $this->parameterName;
    }

    /**
     * @return mixed
     */
    public function getInjectedArgument()
    {
        return $this->injectedArgument;
    }
}