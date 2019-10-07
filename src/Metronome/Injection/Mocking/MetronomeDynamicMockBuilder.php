<?php
namespace Metronome\Injection\Mocking;

class MetronomeDynamicMockBuilder
{
    private $mockClass;
    private $injection;

    public function __construct($className) {
        $this->mockClass = $className;
        $this->injection = array();
    }

    public function method($methodName, $returnedValue) {
        $this->injection[$methodName] = $returnedValue;
    }

    public function build() {
        return MockCreator::mock($this->mockClass, $this->injection);
    }
}