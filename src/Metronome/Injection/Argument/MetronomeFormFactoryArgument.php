<?php
namespace Metronome\Injection\Argument;

class MetronomeFormFactoryArgument extends MetronomeServiceArgument
{
    const FORM_FACTORY_SERVICE_ID = "form.factory";

    public function __construct(string $parameterName) {
        parent::__construct($parameterName, self::FORM_FACTORY_SERVICE_ID);
    }
}