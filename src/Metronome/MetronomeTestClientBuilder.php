<?php
namespace Metronome;

use Metronome\Injection\MetronomeDefinition;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\Definition;

class MetronomeTestClientBuilder
{
    private $projectDir;
    private $bundles;
    private $definitions;

    public function __construct() {
        $this->projectDir = null;
        $this->bundles = array();
        $this->definitions = array();
    }

    public function projectDir($dir) {
        $this->projectDir = $dir;
        return $this;
    }

    public function bundles($bundles) {
        $this->bundles = $bundles;
        return $this;
    }

    public function addBundle($bundle) {
        array_push($this->bundles, $bundle);
        return $this;
    }

    public function definitions($definitions) {
        $this->definitions = $definitions;
        return $this;
    }

    public function controller($className) {
        $definition = new Definition($className);
        $definition->addTag("controller.service_arguments");
        $this->pushDefinition($className, $definition);
        return $this;
    }

    public function addDefinition(MetronomeDefinition $metronomeDefinition) {
        $injectionClass = $metronomeDefinition->getInjectionClass();
        $injectionInterface = $metronomeDefinition->getInjectionInterface();
        $definitionId = ($injectionInterface == null) ? $injectionClass : $injectionInterface;

        $definition = new Definition($injectionClass);
        $definition->setPublic(true);
        $definition->setTags($metronomeDefinition->getInjectionTags());
        $this->pushDefinition($definitionId, $definition);
        return $this;
    }

    public function build() {
        $kernel = new MetronomeTestKernel($this->projectDir, $this->bundles, $this->definitions);
        return new KernelBrowser($kernel);
    }

    private function pushDefinition($id, Definition $definition) {
        $this->definitions[$id] = $definition;
    }
}