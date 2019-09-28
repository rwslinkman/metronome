<?php
namespace Metronome\Injection;

class MetronomeTranslatorArgument extends MetronomeServiceArgument
{
    const TRANSLATOR_SERVICE_ID = "translator";

    public function __construct(string $parameterName)
    {
        parent::__construct($parameterName, self::TRANSLATOR_SERVICE_ID);
    }
}