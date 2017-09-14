<?php
namespace Metronome\Injection;

/**
 * Interface ServiceInjector
 * @package Metronome\Injection
 */
interface ServiceInjector
{
    /**
     * @return string The service name as defined in config.yml
     */
    public function serviceName();

    /**
     * @return string Full namespace for the service to mock
     */
    public function serviceClass();

    /**
     * @return array Key => Value array of methods to mock and their respective results
     */
    public function inject();
}