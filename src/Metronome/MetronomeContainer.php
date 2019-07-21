<?php

namespace Metronome;

use Closure;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;


class MetronomeContainer extends Container
{
    public function set($id, $service)
    {
        // Runs the internal initializer; used by the dumped container to include always-needed files
        if (isset($this->privates['service_container']) && $this->privates['service_container'] instanceof Closure) {
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