<?php
namespace Metronome\Util;

/**
 * Class TestFormData
 * @package Metronome\Util
 */
class MetronomeFormData
{
    /** @var  boolean */
    private $isSubmitted;
    /** @var boolean */
    private $isValid;
    /** @var  array */
    private $submittedData;

    public function __construct($isSubmitted = false, $isValid = false, $data = array()) {
        $this->isSubmitted = $isSubmitted;
        $this->isValid = $isValid;
        $this->submittedData = $data;
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
}