<?php
namespace JappserBundle\Tests\TestEnvironment;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityRepository;
use JappserBundle\Tests\MockBuilder;
use JappserBundle\Tests\TestEnvironment\RepoInjector\RepoInjector;
use JappserBundle\Tests\TestEnvironment\ServiceInjector\ServiceInjector;
use JappserBundle\Tests\TestEnvironment\Util\TestFormData;
use \InvalidArgumentException;
use Mockery\MockInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

/**
 */
class MetronomeBuilder
{
    /** @var Client */
    private $symfonyClient;
    /** @var  EntityRepository */
    private $repository;
    /** @var  boolean */
    private $requiresLogin;
    /** @var ServiceInjector[] */
    private $injectedServices;
    /** @var RepoInjector[] */
    private $injectedRepos;
    /** @var  boolean */
    private $shouldFailFormLogin;
    /** @var  boolean */
    private $mockTemplatingEngine;
    /** @var  TestFormData */
    private $testFormData;
    //
    private $entityManagerLoadAll;
    private $entityManagerLoad;

    public function __construct(Client $client = null, $useHTTPS = true) {
        if($client != null){
            // Force HTTPS in test
            $client->setServerParameter("HTTPS", $useHTTPS);
        }
        $this->symfonyClient = $client;
        $this->repository = null;
        $this->requiresLogin = false;
        $this->injectedServices = array();
        $this->injectedRepos = array();
        $this->shouldFailFormLogin = false;
        $this->mockTemplatingEngine = false;
        $this->entityManagerLoadAll = null;
        $this->entityManagerLoad = null;
    }

    public function injectService(ServiceInjector $serviceInjector) {
        array_push($this->injectedServices, $serviceInjector);
    }

    public function injectRepo(RepoInjector $repoInjector) {
        array_push($this->injectedRepos, $repoInjector);
    }

    public function requiresLogin() {
        $this->requiresLogin = true;
    }

    public function shouldFailFormLogin() {
        $this->shouldFailFormLogin = true;
    }

    public function mockTemplatingEngine() {
        $this->mockTemplatingEngine = true;
    }

    public function mockSymfonyForms(TestFormData $formData) {
        $this->testFormData = $formData;
        $this->mockTemplatingEngine();
    }

    public function genericLoadAll($result) {
        $this->entityManagerLoadAll = $result;
    }

    public function genericLoad($result) {
        $this->entityManagerLoad = $result;
    }

    public function build() {
        if($this->shouldFailFormLogin && $this->requiresLogin) {
            throw new InvalidArgumentException("Cannot use shouldFailFormLogin() and requiresLogin() simultaneously");
        }

        $emMock = $this->buildEntityManager(null);

        $env = new MetronomeEnvironment($this->symfonyClient);
        // Database / Doctrine mock
        $env->injectService('doctrine.orm.default_entity_manager', $emMock);

        // Symfony services mocking
        /** @var ServiceInjector $injectedService */
        foreach ($this->injectedServices as $injectedService) {
            $mock = \Mockery::mock($injectedService->forClass(), $injectedService->inject());
            $env->injectService($injectedService->serviceName(), $mock);
        }

        // Symfony templating engine
        if($this->mockTemplatingEngine) {
            // Test data or default values
            $mockIsSubmitted = ($this->testFormData == null) ? false : $this->testFormData->isSubmitted();
            $mockGetData = ($this->testFormData == null) ? array() : $this->testFormData->getSubmittedData();

            $formMock = MockBuilder::createFormBuilderMock($mockIsSubmitted, $mockGetData);
            $env->injectService("form.factory", $formMock);
            $templatingMock = MockBuilder::createTwigTemplatingMock();
            $env->injectService("templating", $templatingMock);
        }

        $twigMock = MockBuilder::createTwigMock();
        $env->injectService("twig", $twigMock);

        // Logged in status mock
        if($this->requiresLogin) {
            $mockUP = MockBuilder::createMockUserProvider();
            $env->injectService("app.token_authenticator", $mockUP);
        }

        // Login form mock
        if($this->shouldFailFormLogin) {
            $loginError = new CustomUserMessageAuthenticationException("Invalid Credentials");
            $authMock = MockBuilder::createAuthUtilsMock($loginError);
            $env->injectService('security.authentication_utils', $authMock);
        }

        return $env;
    }

    /**
     * @param string $repoClass
     * @return \Doctrine\ORM\EntityManager|\Mockery\MockInterface
     */
    public function buildEntityManager($repoClass = "") {
        $repoMock = null;

        // TODO Show warning when no repo injected and not internal usage
        /** @var RepoInjector $repo */
        foreach ($this->injectedRepos as $repo) {
            if($repo->forRepo() === $repoClass) {
                $repoMock = \Mockery::mock($repo->forClass(), $repo->inject());
                break;
            }
        }
        return MockBuilder::createMockEntityManager($repoMock, null, null, null, $this->entityManagerLoad, $this->entityManagerLoadAll);
    }

    /**
     * @param null $getReference
     * @return ReferenceRepository|MockInterface
     */
    public function buildFixtureReferenceRepoMock($getReference = null) {
        $mockRR = \Mockery::mock('\Doctrine\Common\DataFixtures\ReferenceRepository', array(
            "getReference" => $getReference,
            "hasReference" => $getReference != null,
            "setReference" => null,
            "addReference" => null
        ));
        return $mockRR;
    }
}