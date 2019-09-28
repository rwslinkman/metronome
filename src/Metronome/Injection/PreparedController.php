<?php
namespace Metronome\Injection;


class PreparedController
{
    private $controllerClassName;
    private $controllerArguments;

    /**
     * PreparedController constructor.
     * @param $controllerClassName
     * @param $arguments
     */
    public function __construct($controllerClassName, $arguments)
    {
        $this->controllerClassName = $controllerClassName;
        $this->controllerArguments = $arguments;
    }

    /**
     * @return mixed
     */
    public function getControllerClassName()
    {
        return $this->controllerClassName;
    }

    /**
     * @return mixed
     */
    public function getControllerArguments()
    {
        return $this->controllerArguments;
    }
}