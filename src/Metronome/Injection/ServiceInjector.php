<?php
namespace Metronome\Injection;

interface ServiceInjector
{
    public function serviceName();
    public function serviceClass();
    public function inject();
}