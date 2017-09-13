<?php
namespace JappserBundle\Tests\TestEnvironment\ServiceInjector;

interface ServiceInjector
{
    public function serviceName();
    public function serviceClass();
    public function inject();
}