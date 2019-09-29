<?php
namespace Metronome\Injection;

class MetronomeDefinition
{
    /** @var string */
    private $injectionClass;
    /** @var string */
    private $injectionInterface;
    /** @var array */
    private $injectionTags;

    public function __construct($injectionClass, $injectionInterface = null, $tags = array())
    {
        $this->injectionClass = $injectionClass;
        $this->injectionInterface = $injectionInterface;
        $this->injectionTags = $tags;
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

    /**
     * @return array
     */
    public function getInjectionTags()
    {
        return $this->injectionTags;
    }
}