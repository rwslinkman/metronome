<?php
namespace Metronome;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MetronomeTest extends WebTestCase
{
    protected static function bootKernel(array $options = [])
    {
        static::ensureKernelShutdown();

        static::$kernel = static::createKernel($options);
        static::$kernel->boot();

        $container = static::$kernel->getContainer();
        static::$container = new MetronomeContainer();

        return static::$kernel;
    }

}