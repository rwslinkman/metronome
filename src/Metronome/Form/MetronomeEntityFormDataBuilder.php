<?php
namespace Metronome\Form;


use Symfony\Component\Form\FormError;

class MetronomeEntityFormDataBuilder
{
    private $isValid;
    private $formData;
    private $errors;

    public function __construct()
    {
        $this->isValid = false;
        $this->formData = null;
        $this->errors = array();
    }

    public function isValid($isValid) {
        $this->isValid = $isValid;
        return $this;
    }

    public function formData($entity) {
        $this->formData = $entity;
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