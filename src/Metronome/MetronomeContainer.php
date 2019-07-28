<?php
namespace Metronome;

use Symfony\Bundle\FrameworkBundle\Test\TestContainer as BaseTestContainer;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class MetronomeContainer extends BaseTestContainer
{
    private $publicContainer;

    public function set($id, $service)
    {
        $this->internalSet($id, $service);

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

    private function internalSet($id, $service) {
        // Runs the internal initializer; used by the dumped container to include always-needed files
        if (isset($this->privates['service_container']) && $this->privates['service_container'] instanceof \Closure) {
            $initialize = $this->privates['service_container'];
            unset($this->privates['service_container']);
            $initialize();
        }

        if ('service_container' === $id) {
            throw new InvalidArgumentException('You cannot set service "service_container".');
        }

        if (isset($this->aliases[$id])) {
            unset($this->aliases[$id]);
        }

        if (null === $service) {
            unset($this->services[$id]);

            return;
        }

        $this->services[$id] = $service;
    }
}