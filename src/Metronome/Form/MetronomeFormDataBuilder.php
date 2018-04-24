<?php
namespace Metronome\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormError;

/**
 * Class MetronomeFormDataBuilder
 * @package Metronome\Form
 */
class MetronomeFormDataBuilder
{
    private $formType;
    private $isValid;
    private $formData;
    private $errors;

    public function __construct()
    {
        $this->isValid = false;
        $this->formData = array();
        $this->errors = array();
    }

    public function isValid($isValid) {
        $this->isValid = $isValid;
        return $this;
    }

    public function formData(AbstractType $formType, $field, $submittedValue) {
        $formName = $formType->getBlockPrefix();
        if(!array_key_exists($formName, $this->formData)) {
            $this->formData[$formName] = array();
        }

        if(!array_key_exists($field, $this->formData[$formName])) {
            $this->formData[$formName][$field] = "";
        }

        $this->formData[$formName][$field] = $submittedValue;
        return $this;
    }

    public function error($errorName, FormError $error) {
        if(!array_key_exists($errorName, $this->errors)) {
            $this->errors[$errorName] = "";
        }

        $this->errors[$errorName] = $error;
        return $this;
    }

    public function build() {
        return new MetronomeFormData(true, $this->isValid, $this->formData, $this->errors);
    }
}