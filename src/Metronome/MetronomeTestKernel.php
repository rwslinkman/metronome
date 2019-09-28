<?php
namespace Metronome;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Exception;
use Metronome\Injection\MetronomeArgument;
use Metronome\Injection\MetronomeFunctionArgumentDefinition;
use Metronome\Injection\MetronomeServiceArgument;
use Metronome\Injection\MockCreator;
use Metronome\Injection\PreparedController;
use Metronome\Injection\ServiceInjector;
use RDV\SymfonyContainerMocks\DependencyInjection\TestKernelTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    /** @var PreparedController  */
    private $preparedController;
    private $injections;

    public function __construct($projectDir = null, $additionalBundles = array(), PreparedController $controllerClass = null, $containerInjections = array())
    {
        parent::__construct('test', true);
        $this->projectDir = $projectDir;
        $this->additionalBundles = array_merge($additionalBundles, array(
            new FrameworkBundle(),
            new DoctrineBundle()
        ));
        $this->preparedController = $controllerClass;
        $this->injections = $containerInjections;

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

        if($this->preparedController != null) {
            /** @var MetronomeFunctionArgumentDefinition $functionArgumentDefinition */
            foreach($this->preparedController->getFunctionArgumentDefinition() as $functionArgumentDefinition) {
                $definitionInterface = $functionArgumentDefinition->getInjectionInterface();
                if($definitionInterface == null) {
                    $definitionInterface = $functionArgumentDefinition->getInjectionClass();
                }
                $definition = new Definition($functionArgumentDefinition->getInjectionClass());
                $container->setDefinition($definitionInterface, $definition);
            }

            // Controller definition
            $definition = new Definition($this->preparedController->getControllerClassName());
            $definition->addTag("controller.service_arguments");
            $container->setDefinition($this->preparedController->getControllerClassName(), $definition);

            // Controller instantiation and injection into container
            $controller = $this->prepareController($this->preparedController);
            $controller->setContainer($container);
            $this->inject($this->preparedController->getControllerClassName(), $controller);
        }

        // Inject services
        foreach($this->injections as $serviceName => $injection) {
            $this->inject($serviceName, $injection);
        }
    }

    private function projectDir() {
        if($this->projectDir != null) {
            return $this->projectDir;
        }
        return $this->getProjectDir()."/../../..";
    }

    private function inject($serviceId, $mock) {
        $this->getContainer()->set($serviceId, $mock);
    }

    /**
     * @param PreparedController $preparedController
     * @return AbstractController|null
     */
    private function prepareController(PreparedController $preparedController) {
        $argumentObjects = array();
        /** @var MetronomeArgument $definition */
        foreach($preparedController->getConstructorArguments() as $definition) {
            if($definition instanceof MetronomeArgument == false) {
                throw new \InvalidArgumentException("Argument must be of type MetronomeArgument");
            }

            $argument = $definition->getInjectedArgument();
            if($definition instanceof MetronomeServiceArgument) {
                $argument = $this->getContainer()->get($definition->getServiceId());
            }

            $argumentObjects[$definition->getParameterName()] = $argument;
        }

        try {
            $controllerInstance = null;
            $reflectionController = new \ReflectionClass($preparedController->getControllerClassName());
            $reflectionConstructor = $reflectionController->getConstructor();

            if($reflectionConstructor != null) {
                $parameters = $reflectionConstructor->getParameters();

                $arguments = array();
                foreach($parameters as $parameter) {
                    if(array_key_exists($parameter->name, $argumentObjects)) {
                        $arguments[$parameter->name] = $argumentObjects[$parameter->name];
                    } else {
                        throw new \InvalidArgumentException(sprintf("Please provide parameter '%s'", $parameter->name));
                    }
                }

                $controllerInstance = $reflectionController->newInstanceArgs($arguments);
            } else {
                // No constructor defined
                $controllerInstance = $reflectionController->newInstanceWithoutConstructor();
            }

            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return $controllerInstance;
        } catch (\ReflectionException $e) {
            var_dump($e);
        }
        return null;
    }
}