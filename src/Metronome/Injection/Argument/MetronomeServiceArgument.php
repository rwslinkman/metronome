<?php
namespace Metronome\Injection\Argument;

class MetronomeServiceArgument extends MetronomeArgument
{
    private $serviceId;

    public function __construct(string $parameterName, string $serviceId)
    {
        parent::__construct($parameterName, null);
        $this->serviceId = $serviceId;
    }

    /**
     * @return string
     */
    public function getServiceId(): string
    {
        return $this->serviceId;
    }
}