<?php
namespace Metronome\Injection;


class PreparedController
{
    private $controllerClassName;
    private $controllerInstance;

    /**
     * PreparedController constructor.
     * @param $controllerClassName
     * @param $controllerMock
     */
    public function __construct($controllerClassName, $controllerMock)
    {
        $this->controllerClassName = $controllerClassName;
        $this->controllerInstance = $controllerMock;
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
    public function getControllerInstance()
    {
        return $this->controllerInstance;
    }
}