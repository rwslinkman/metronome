<?php
namespace Metronome;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class MetronomeTestClientBuilder
{
    private $projectDir;
    private $bundles;
    private $controller;

    public function __construct() {
        $this->projectDir = null;
        $this->bundles = array();
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

    public function controller($className) {
        $this->controller = $className;
        return $this;
    }

    public function build() {
        $kernel = new MetronomeTestKernel($this->projectDir, $this->bundles, $this->controller);
        return new KernelBrowser($kernel);
    }
}