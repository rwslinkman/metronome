<?php
namespace Metronome\Injection;

class MetronomeArgument
{
    /** @var string */
    private $injectedServiceId;
    /** @var string */
    private $parameterName;

    /**
     * MetronomeArgument constructor.
     * @param string $injectedServiceId
     * @param string $parameterName
     */
    public function __construct(string $injectedServiceId, string $parameterName)
    {
        $this->injectedServiceId = $injectedServiceId;
        $this->parameterName = $parameterName;
    }

    /**
     * @return string
     */
    public function getInjectedServiceId(): string
    {
        return $this->injectedServiceId;
    }

    /**
     * @return string
     */
    public function getParameterName(): string
    {
        return $this->parameterName;
    }
}