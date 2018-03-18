<?php
namespace Metronome;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityRepository;
use \InvalidArgumentException;
use Metronome\Injection\MetronomeFormData;
use Metronome\Injection\MetronomeLoginData;
use Metronome\Injection\MockBuilder;
use Metronome\Injection\RepoInjector;
use Metronome\Injection\ServiceInjector;
use Mockery\MockInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

/**
 * Class MetronomeBuilder
 * @package Metronome
 * @author Rick Slinkman
 */
class MetronomeBuilder
{
    /** @var Client */
    private $symfonyClient;
    /** @var  EntityRepository */
    private $repository;
    /** @var MetronomeLoginData */
    private $requiresLogin;
    /** @var ServiceInjector[] */
    private $injectedServices;
    /** @var RepoInjector[] */
    private $injectedRepos;
    /** @var  boolean */
    private $shouldFailFormLogin;
    /** @var  boolean */
    private $mockSymfonyForms;
    /** @var  MetronomeFormData */
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
        $this->requiresLogin = null;
        $this->injectedServices = array();
        $this->injectedRepos = array();
        $this->shouldFailFormLogin = false;
        $this->mockSymfonyForms = false;
        $this->entityManagerLoadAll = null;
        $this->entityManagerLoad = null;
    }

    public function injectService(ServiceInjector $serviceInjector) {
        array_push($this->injectedServices, $serviceInjector);
    }

    public function injectRepo(RepoInjector $repoInjector) {
        array_push($this->injectedRepos, $repoInjector);
    }

    public function requiresLogin(MetronomeLoginData $injected = null) {
        if($injected == null) {
            $injected = MetronomeLoginData::defaultLoginData();
        }
        $this->requiresLogin = $injected;
    }

    public function shouldFailFormLogin() {
        $this->shouldFailFormLogin = true;
    }

    public function mockSymfonyForms(MetronomeFormData $formData) {
        $this->testFormData = $formData;
        $this->mockSymfonyForms = true;
    }

    public function genericLoadAll($result) {
        $this->entityManagerLoadAll = $result;
    }

    public function genericLoad($result) {
        $this->entityManagerLoad = $result;
    }

    /**
     * @return MetronomeEnvironment
     */
    public function build() {
        if($this->shouldFailFormLogin && $this->requiresLogin) {
            throw new InvalidArgumentException("Cannot use shouldFailFormLogin() and requiresLogin() simultaneously");
        }

        $emMock = $this->buildEntityManager(null);

        $env = new MetronomeEnvironment($this->symfonyClient);
        // Database / Doctrine mock
        $env->injectService('doctrine.orm.entity_manager', $emMock);

        // Symfony services mocking
        /** @var ServiceInjector $injectedService */
        foreach ($this->injectedServices as $injectedService) {
            $mock = \Mockery::mock($injectedService->serviceClass(), $injectedService->inject());
            $env->injectService($injectedService->serviceName(), $mock);
        }

        // Symfony templating engine
        if($this->mockSymfonyForms) {
            // Test data or default values
            $mockIsSubmitted = ($this->testFormData == null) ? false : $this->testFormData->isSubmitted();
            $mockIsValid = ($this->testFormData == null) ? false : $this->testFormData->isValid();
            $mockGetData = ($this->testFormData == null) ? array() : $this->testFormData->getSubmittedData();

            // TODO This rendering can be improved, it's only used when mocking forms
            $formMock = MockBuilder::createFormBuilderMock($mockIsSubmitted, $mockIsValid, $mockGetData);
            $env->injectService("form.factory", $formMock);
            // TODO This mock can be removed when FormView is succesfully mocked
            $templatingMock = MockBuilder::createTwigTemplatingMock();
            $env->injectService("templating", $templatingMock);
        }

        // Logged in status mock
        if($this->requiresLogin != null) {
            $mockUP = MockBuilder::createMockUserProvider($this->requiresLogin->getUser());
            $env->injectService($this->requiresLogin->getAuthenticatorService(), $mockUP);
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
            if($repo->repositoryName() === $repoClass) {
                $repoMock = \Mockery::mock($repo->repositoryClass(), $repo->inject());
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