<?php
namespace Metronome;

use Metronome\Injection\MockCreator;
use Metronome\Injection\PreparedController;
use Metronome\Injection\ServiceInjector;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class MetronomeTestClientBuilder
{
    private $projectDir;
    private $bundles;
    private $controller;
    private $containerInjections;

    public function __construct() {
        $this->projectDir = null;
        $this->bundles = array();
        $this->controller = null;
        $this->containerInjections = array();
    }

    public function projectDir($dir) {
        $this->projectDir = $dir;
        return $this;
    }

    public function addBundle($bundle) {
        array_push($this->bundles, $bundle);
        return $this;
    }

    public function bundles($bundles) {
        $this->bundles = $bundles;
        return $this;
    }

    public function controller(PreparedController $className) {
        $this->controller = $className;
        return $this;
    }

    public function containerInjections($containerInjections) {
        $this->containerInjections = $containerInjections;
        return $this;
    }

    public function addContainerInjection($serviceId, $mockObject) {
        $this->containerInjections[$serviceId] = $mockObject;
    }

    public function injectObject($serviceName, $anObject) {
        $this->addContainerInjection($serviceName, $anObject);
    }

    public function injectService(ServiceInjector $serviceInjector) {
        $injectedServiceMock = MockCreator::mock($serviceInjector->serviceClass(), $serviceInjector->inject());
        $this->addContainerInjection($serviceInjector->serviceName(), $injectedServiceMock);
    }

    public function build() {
        $kernel = new MetronomeTestKernel($this->projectDir, $this->bundles, $this->controller, $this->containerInjections);
        return new KernelBrowser($kernel);
    }
}