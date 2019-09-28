<?php
namespace Metronome;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Exception;
use Metronome\Injection\MetronomeDefinition;
use Metronome\Injection\MockCreator;
use RDV\SymfonyContainerMocks\DependencyInjection\TestKernelTrait;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class MetronomeTestKernel extends Kernel
{
    use MicroKernelTrait, TestKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    private $projectDir;
    private $additionalBundles;
    private $controllerClass;
    private $definitions;

    public function __construct($projectDir = null, $additionalBundles = array(), $controllerClass = null, $definitions = array())
    {
        parent::__construct('test', true);
        $this->projectDir = $projectDir;
        $this->additionalBundles = array_merge(array(
            new FrameworkBundle(),
            new DoctrineBundle()
        ), $additionalBundles);
        $this->controllerClass = $controllerClass;
        $this->definitions = $definitions;
    }
    public function registerBundles()
    {
        return $this->additionalBundles;
    }

    public function getCacheDir()
    {
        return $this->projectDir().'/var/cache/'.spl_object_hash($this);
    }

    /**
     * Add or import routes into your application.
     *
     *     $routes->import('config/routing.yml');
     *     $routes->add('/admin', 'App\Controller\AdminController::dashboard', 'admin_dashboard');
     *
     * @param RouteCollectionBuilder $routes
     * @throws LoaderLoadException
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = $this->projectDir().'/config';

        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');
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

        /** @var MetronomeDefinition $metronomeDefinition */
        foreach($this->definitions as $metronomeDefinition) {
            $injectionClass = $metronomeDefinition->getInjectionClass();
            $definitionId = ($metronomeDefinition->getInjectionInterface() == null) ? $injectionClass : null;
            $definition = new Definition($injectionClass);
            $container->setDefinition($definitionId, $definition);
        }

        if($this->controllerClass != null) {
            $definition = new Definition($this->controllerClass);
            $definition->addTag("controller.service_arguments");
            $container->setDefinition($this->controllerClass, $definition);
        }
    }

    private function projectDir() {
        if($this->projectDir != null) {
            return $this->projectDir;
        }
        return $this->getProjectDir()."/../../..";
    }
}