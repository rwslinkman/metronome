<?php
namespace Metronome\Injection;


class PreparedController
{
    /** @var string */
    private $controllerClassName;
    /** @var array|MetronomeArgument */
    private $constructorArguments;
    /** @var array|MetronomeFunctionArgumentDefinition */
    private $functionArgumentDefinition;

    /**
     * PreparedController constructor.
     * @param $controllerClassName
     * @param $constructorArguments
     * @param $functionArgumentDefinition
     */
    public function __construct($controllerClassName, $constructorArguments, $functionArgumentDefinition)
    {
        $this->controllerClassName = $controllerClassName;
        $this->constructorArguments = $constructorArguments;
        $this->functionArgumentDefinition = $functionArgumentDefinition;
    }

    /**
     * @return string
     */
    public function getControllerClassName()
    {
        return $this->controllerClassName;
    }

    /**
     * @return array|MetronomeArgument
     */
    public function getConstructorArguments()
    {
        return $this->constructorArguments;
    }

    /**
     * @return array|MetronomeFunctionArgumentDefinition
     */
    public function getFunctionArgumentDefinition()
    {
        return $this->functionArgumentDefinition;
    }


}