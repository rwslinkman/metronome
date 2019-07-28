<?php
namespace Metronome;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class MetronomeKernel extends BaseKernel {

    use MicroKernelTrait;

    public function getOriginalContainer()
    {
        if(!$this->container) {
            parent::boot();
        }

        /** @var Container $container */
        return $this->container;
    }

    public function getContainer()
    {
        if ($this->environment == 'prod') {
            return parent::getContainer();
        }

        /** @var Container $container */
        $container = $this->getOriginalContainer();

        $testContainer = $container->get('my.test.service_container');

        $testContainer->setPublicContainer($container);

        return $testContainer;
    }

    /**
     * Returns an array of bundles to register.
     *
     * @return iterable|BundleInterface[] An iterable of bundle instances
     */
    public function registerBundles()
    {
        $bundles = array(
            FrameworkBundle::class => ['all' => true],
//            SecurityBundle::class => ['all' => true],
//            DoctrineCacheBundle::class => ['all' => true],
            DoctrineBundle::class => ['all' => true],
//            DoctrineFixturesBundle::class => ['all' => true]
        );
        foreach ($bundles as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    /**
     * Add or import routes into your application.
     *
     *     $routes->import('config/routing.yml');
     *     $routes->add('/admin', 'App\Controller\AdminController::dashboard', 'admin_dashboard');
     *
     * @param RouteCollectionBuilder $routes
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        // TODO: Implement configureRoutes() method.
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
     * @param ContainerBuilder $c
     * @param LoaderInterface $loader
     */
    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        // TODO: Implement configureContainer() method.
    }
}