<?php
namespace Metronome;

use Symfony\Bundle\FrameworkBundle\Test\TestContainer as BaseTestContainer;

class MetronomeContainer extends BaseTestContainer
{
    private $publicContainer;

    public function set($id, $service)
    {
        $r = new \ReflectionObject($this->publicContainer);
        $p = $r->getProperty('services');
        $p->setAccessible(true);

        $services = $p->getValue($this->publicContainer);

        $services[$id] = $service;

        $p->setValue($this->publicContainer, $services);
    }

    public function setPublicContainer($container)
    {
        $this->publicContainer = $container;
    }
}