<?php
namespace Metronome\Injection;

class MetronomeDefinition
{
    /** @var string */
    private $injectionClass;
    /** @var string */
    private $injectionInterface;

    public function __construct($injectionClass, $injectionInterface = null)
    {
        $this->injectionClass = $injectionClass;
        $this->injectionInterface = $injectionInterface;
    }

    /**
     * @return string
     */
    public function getInjectionClass()
    {
        return $this->injectionClass;
    }

    /**
     * @return string
     */
    public function getInjectionInterface()
    {
        return $this->injectionInterface;
    }

}