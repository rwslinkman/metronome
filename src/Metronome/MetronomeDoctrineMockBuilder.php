<?php
namespace Metronome;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Metronome\Injection\Mocking\MockBuilder;
use Metronome\Injection\Mocking\MockCreator;
use Metronome\Injection\RepoInjector;
use Mockery\MockInterface;

class MetronomeDoctrineMockBuilder
{
    /** @var RepoInjector[] */
    private $injectedRepos;
    private $entityManagerLoadAll;
    private $entityManagerLoad;

    public function __construct()
    {
        $this->injectedRepos = array();
        $this->entityManagerLoadAll = null;
        $this->entityManagerLoad = null;
    }

    public function injectRepo(RepoInjector $repoInjector) {
        array_push($this->injectedRepos, $repoInjector);
    }

    public function genericLoadAll($result) {
        $this->entityManagerLoadAll = $result;
    }

    public function genericLoad($result) {
        $this->entityManagerLoad = $result;
    }

    /**
     * @param string $repoClass
     * @return \Doctrine\ORM\EntityManager|\Mockery\MockInterface|\Doctrine\ORM\EntityManagerInterface
     */
    public function buildEntityManager($repoClass = "") {
        $repoMock = null;

        // TODO Show warning when no repo injected and not internal usage
        /** @var RepoInjector $repo */
        foreach ($this->injectedRepos as $repo) {
            if($repo->repositoryName() === $repoClass) {
                $repoMock = MockCreator::mock($repo->repositoryClass(), $repo->inject());
                break;
            }
        }
        return MockBuilder::createMockEntityManager($repoMock, null, null, null, $this->entityManagerLoad, $this->entityManagerLoadAll);
    }

    public function buildSingleRepositoryEntityManager(RepoInjector $injector) {
        $this->injectRepo($injector);
        return $this->buildEntityManager($injector->repositoryClass());
    }

    /**
     * @param null $getReference
     * @return ReferenceRepository|MockInterface
     */
    public function buildFixtureReferenceRepoMock($getReference = null) {
        $mockRR = MockCreator::mock('\Doctrine\Common\DataFixtures\ReferenceRepository', array(
            "getReference" => $getReference,
            "hasReference" => $getReference != null,
            "setReference" => null,
            "addReference" => null
        ));
        return $mockRR;
    }

    /**
     * @param string $entityClass The name of the Entity to mock
     * @return ManagerRegistry|MockInterface
     */
    public function buildManagerRegistryMock($entityClass = null) {
        //
        $mockEM = MockBuilder::createMockEntityManager(null, null, null, null,
            $this->entityManagerLoad, $this->entityManagerLoadAll, $entityClass);
        //
        $mockMR = MockCreator::mock('Doctrine\Persistence\ManagerRegistry', array(
            "getManagerForClass" => $mockEM,
        ));
        return $mockMR;
    }
}
