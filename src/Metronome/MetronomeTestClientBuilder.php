<?php
namespace Metronome;

use Metronome\Injection\MetronomeDefinition;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class MetronomeTestClientBuilder
{
    private $projectDir;
    private $bundles;
    private $controller;
    private $definitions;

    public function __construct() {
        $this->projectDir = null;
        $this->bundles = array();
        $this->controller = null;
        $this->definitions = array();
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

    public function definitions($definitions) {
        $this->definitions = $definitions;
        return $this;
    }

    public function addDefinition(MetronomeDefinition $definition) {
        array_push($this->definitions, $definition);
        return $this;
    }

    public function build() {
        $kernel = new MetronomeTestKernel($this->projectDir, $this->bundles, $this->controller, $this->definitions);
        return new KernelBrowser($kernel);
    }
}