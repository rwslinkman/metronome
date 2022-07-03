<?php
namespace Metronome;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Exception;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class MetronomeTestKernel extends Kernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    private $projectDir;
    private $additionalBundles;
    private $definitions;

    public function __construct($projectDir = null, $additionalBundles = array(), $definitions = array())
    {
        parent::__construct('test', true);
        $this->projectDir = $projectDir;
        $this->additionalBundles = array_merge(array(
            new FrameworkBundle(),
            new DoctrineBundle()
        ), $additionalBundles);
        $this->definitions = $definitions;
    }

    public function registerBundles(): iterable
    {
        return $this->additionalBundles;
    }

    public function getCacheDir(): string
    {
        return $this->projectDir().'/var/cache/'.spl_object_hash($this);
    }

    /**
     * Configures the container.
     *
     * You can register extensions:
     *
     *     $c->loadFromExtension('framework', [
     *         'secret' => '%secret%'
     *     ]);
     *
     * Or services:
     *
     *     $c->register('halloween', 'FooBundle\HalloweenProvider');
     *
     * Or parameters:
     *
     *     $c->setParameter('halloween', 'lot of fun');
     *
     * @param ContainerBuilder $container
     * @param LoaderInterface $loader
     * @throws Exception
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->addResource(new FileResource($this->projectDir().'/config/bundles.php'));
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);

        $container->loadFromExtension("framework", array(
            "secret" => "someSecret"
        ));

        foreach($this->definitions as $definitionId => $definition) {
            $container->setDefinition($definitionId, $definition);
        }
    }

    private function projectDir() {
        if($this->projectDir != null) {
            return $this->projectDir;
        }
        return $this->getProjectDir()."/../../..";
    }
}