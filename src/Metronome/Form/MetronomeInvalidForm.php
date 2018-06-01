<?php
namespace Metronome\Form;


/**
 * Class MetronomeInvalidForm
 * Represents a submitted form that did not pass the FormType's constraints
 * Use this to mock forms that you want to bypass in tests
 * @package Metronome\Form
 */
class MetronomeInvalidForm extends MetronomeFormData
{
    public function __construct()
    {
        parent::__construct(true);
    }

}