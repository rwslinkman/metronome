<?php
namespace Metronome\Form;

/**
 * Class MetronomeNonSubmittedForm
 * Represents a non-submitted form
 * Use this to mock forms that you want to bypass in tests
 * @package Metronome\Form
 */
class MetronomeNonSubmittedForm extends MetronomeFormData
{
    public function __construct()
    {
        parent::__construct();
    }

}