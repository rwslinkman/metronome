<?php
namespace Metronome;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use \InvalidArgumentException;
use Metronome\Form\MetronomeFormData;
use Metronome\Injection\MetronomeLoginData;
use Metronome\Injection\MockBuilder;
use Metronome\Injection\RepoInjector;
use Metronome\Injection\ServiceInjector;
use Metronome\Util\MetronomeAuthenticationException;
use Metronome\Util\ServiceEnum;
use Mockery\MockInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Class MetronomeBuilder
 * Helps you set-up a multitude of mocks to test your Symfony application.
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
    private $loginData;
    /** @var ServiceInjector[] */
    private $injectedServices;
    /** @var RepoInjector[] */
    private $injectedRepos;
    /**
     * @var  boolean
     * @deprecated
     */
    private $shouldFailFormLogin;
    /** @var AuthenticationException */
    private $authException;
    /**
     * @var  boolean
     * @deprecated
     */
    private $mockSymfonyForms;
    /** @var  MetronomeFormData */
    private $testFormData;
    /** @var array|MetronomeFormData[] */
    private $injectedForms;
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
        $this->loginData = null;
        $this->injectedServices = array();
        $this->injectedRepos = array();
        $this->injectedForms = array();
        $this->shouldFailFormLogin = false;
        $this->authException = null;
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
        $this->loginData = $injected;
    }

    /**
     * Sets an error message for then authentication security service.
     * Use this when you want to test your login form.
     * @param string $errorMessage
     */
    public function injectAuthenticationError($errorMessage = "Invalid credentials") {
        $this->authException = new CustomUserMessageAuthenticationException($errorMessage);
    }

    /**
     * @deprecated use MetronomeBuilder->injectAuthenticationError();
     */
    public function shouldFailFormLogin() {
        $this->shouldFailFormLogin = true;
    }

    /**
     * Allows to inject mocked form to use in your Controller tests
     * When calling this multiple times with different MetronomeFormData, they will be returned in the order they were injected.
     * @param MetronomeFormData $formData
     */
    public function injectForm(MetronomeFormData $formData) {
        array_push($this->injectedForms, $formData);
    }

    /**
     * @deprecated Replaced by MetronomeBuilder->injectForm() that can handle multiple forms in 1 controller
     * @param MetronomeFormData $formData
     */
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
        $this->verifyState();

        $testContainer = new MetronomeContainer($this->symfonyClient->getKernel(), 'my.test.service_container');
        $testContainer->setPublicContainer($this->symfonyClient->getContainer()->get("test.service_container"));

        $emMock = $this->buildEntityManager(null);
        // Database / Doctrine mock
        $testContainer->set(ServiceEnum::ENTITY_MANAGER, $emMock);

        // Symfony services mocking
        /** @var ServiceInjector $injectedService */
        foreach ($this->injectedServices as $injectedService) {
            $injectedServiceMock = \Mockery::mock($injectedService->serviceClass(), $injectedService->inject());
            $testContainer->set($injectedService->serviceName(), $injectedServiceMock);
        }

        // Symfony templating engine
        if($this->mockSymfonyForms) {
            // Test data or default values
            $mockIsSubmitted = ($this->testFormData == null) ? false : $this->testFormData->isSubmitted();
            $mockIsValid = ($this->testFormData == null) ? false : $this->testFormData->isValid();
            $mockGetData = ($this->testFormData == null) ? array() : $this->testFormData->getSubmittedData();
            $mockErrors = ($this->testFormData == null) ? array() : $this->testFormData->getErrors();

            // TODO This rendering can be improved, it's only used when mocking forms
            $formMock = MockBuilder::createFormBuilderMock($mockIsSubmitted, $mockIsValid, $mockGetData, $mockErrors);
            $testContainer->set(ServiceEnum::FORM_FACTORY, $formMock);
            // TODO This mock can be removed when FormView is succesfully mocked
            $templatingMock = MockBuilder::createTwigEnvironment();
            $testContainer->set(ServiceEnum::TEMPLATING, $templatingMock);
        }

        if(!empty($this->injectedForms)) {
            $formMock = MockBuilder::createFormFactoryMock($this->injectedForms);
            // TODO This rendering can be improved, it's only used when mocking forms
            $testContainer->set(ServiceEnum::FORM_FACTORY, $formMock);
            // TODO This mock can be removed when FormView is succesfully mocked
            $templatingMock = MockBuilder::createTwigEnvironment();
            $testContainer->set(ServiceEnum::TEMPLATING, $templatingMock);
            $testContainer->set(ServiceEnum::TWIG, $templatingMock);
        }

        // Logged in status mock
        if($this->loginData != null) {
            $mockUser = $this->loginData->getUser();
            $token = new PostAuthenticationGuardToken($mockUser, "dev", $mockUser->getRoles());
            foreach ($this->loginData->getTokenAttributes() as $attribute => $value) {
                $token->setAttribute($attribute, $value);
            }

            $mockUP = MockBuilder::createMockUserProvider($token);
            $mockTokenStorage = MockBuilder::createTokenStorageMock($token);

            $testContainer->set($this->loginData->getAuthenticatorService(), $mockUP);
            $testContainer->set(ServiceEnum::SECURITY_TOKEN_STORAGE, $mockTokenStorage);
        }

        // Login form mock
        if($this->shouldFailFormLogin || ($this->authException != null)) {
            if($this->authException == null) {
                $this->authException = new MetronomeAuthenticationException("Invalid Credentials");
            }
            $authMock = MockBuilder::createAuthUtilsMock($this->authException);
            $testContainer->set(ServiceEnum::SECURITY_AUTH_UTILS, $authMock);
        }

        $env = new MetronomeEnvironment($this->symfonyClient);
        $env->injectTestContainer($testContainer);

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

    /**
     * @param string $entityClass The name of the Entity to mock
     * @return ManagerRegistry|MockInterface
     */
    public function buildManagerRegistryMock($entityClass = null) {
        //
        $mockEM = MockBuilder::createMockEntityManager(null, null, null, null,
            $this->entityManagerLoad, $this->entityManagerLoadAll, $entityClass);
        //
        $mockMR = \Mockery::mock('Doctrine\Common\Persistence\ManagerRegistry', array(
            "getManagerForClass" => $mockEM,
        ));
        return $mockMR;
    }

    private function verifyState(): void
    {
        if ($this->shouldFailFormLogin && $this->loginData) {
            throw new InvalidArgumentException("Cannot use shouldFailFormLogin() and requiresLogin() simultaneously");
        }
        if ($this->authException && $this->loginData) {
            throw new InvalidArgumentException("Cannot use injectAuthenticationError() and requiresLogin() simultaneously");
        }
        if (!empty($this->injectedForms) && $this->mockSymfonyForms) {
            throw new InvalidArgumentException("Cannot use injectForm() and mockSymfonyForms() simutaneously");
        }
    }
}