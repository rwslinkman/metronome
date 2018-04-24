<?php
namespace Metronome\Form;

/**
 * Class TestFormData
 * @package Metronome\Injection
 */
class MetronomeFormData
{
    /** @var  boolean */
    private $isSubmitted;
    /** @var boolean */
    private $isValid;
    /** @var  array */
    private $submittedData;
    /** @var array */
    private $errors;

    public function __construct($isSubmitted = false, $isValid = false, $data = array(), $errors = array()) {
        $this->isSubmitted = $isSubmitted;
        $this->isValid = $isValid;
        $this->submittedData = $data;
        $this->errors = $errors;
    }

    /**
     * @return bool
     */
    public function isSubmitted() {
        return $this->isSubmitted;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * @return array
     */
    public function getSubmittedData() {
        return $this->submittedData;
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
}