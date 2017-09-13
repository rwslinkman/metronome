<?php
namespace JappserBundle\Tests\TestEnvironment\Util;

class TestFormData
{
    /** @var  boolean */
    private $isSubmitted;
    /** @var  array */
    private $submittedData;

    public function __construct($isSubmitted = false, $data = array()) {
        $this->isSubmitted = $isSubmitted;
        $this->submittedData = $data;
    }

    public function isSubmitted() {
        return $this->isSubmitted;
    }

    public function getSubmittedData() {
        return $this->submittedData;
    }
}