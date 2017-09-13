<?php
namespace JappserBundle\Tests\TestEnvironment\Util;


use JappserBundle\Service\Helper\FileReader;

class TestFileReader extends FileReader
{
    private $result;
    private $throwsException;

    public function __construct($result = array(), $throws = false) {
        $this->result = $result;
        $this->throwsException = $throws;
    }

    public function readFile($filePath) {
        if($this->throwsException) {
            throw new \Exception("Test exception");
        }
        return $this->result;
    }

    public function setResult($result) {
        $this->result = $result;
    }

    public function throwException() {
        $this->throwsException = true;
    }
}