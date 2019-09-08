<?php
namespace Metronome;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use \InvalidArgumentException;
use Metronome\Form\MetronomeFormData;
use Metronome\Injection\MetronomeArgument;
use Metronome\Injection\MetronomeLoginData;
use Metronome\Injection\MockBuilder;
use Metronome\Injection\MockCreator;
use Metronome\Injection\PreparedController;
use Metronome\Injection\RepoInjector;
use Metronome\Injection\ServiceInjector;
use Metronome\Util\MetronomeAuthenticationException;
use Metronome\Util\ServiceEnum;
use Mockery\MockInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

/**
 * Class MetronomeBuilder
 * Helps you set-up a multitude of mocks to test your Symfony application.
 * @package Metronome
 * @author Rick Slinkman
 */
class MetronomeBuilder
{
    /** @var KernelBrowser */
    private $testClient;
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
    private $entityManagerLoadAll;
    private $entityManagerLoad;
    /** @var PreparedController */
    private $preparedController;
    /** @var ParameterBag */
    private $parameterBag;

    public function __construct(KernelBrowser $client = null, $useHTTPS = true) {
        if($client != null){
            // Force HTTPS in test
            $client->setServerParameter("HTTPS", $useHTTPS);
        }
        $this->testClient = $client;
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
        $this->preparedController = null;
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

    public function injectParameter($param, $value) {
        if($this->parameterBag == null) {
            $this->parameterBag = new ParameterBag();
        }
        $this->parameterBag->set($param, $value);
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

    public function setupController($controllerClass, $parameterDefinitions) {
        $this->preparedController = new PreparedController($controllerClass, $parameterDefinitions);
    }

    /**
     * @return MetronomeEnvironment
     */
    public function build() {
        $this->verifyState();

        $emMock = $this->buildEntityManager(null);
        // Database / Doctrine mock
        $this->testClient->getKernel()->boot();
        $this->inject(ServiceEnum::ENTITY_MANAGER, $emMock);
        $this->inject("doctrine.orm.default_entity_manager", $emMock);

        // Symfony services mocking
        /** @var ServiceInjector $injectedService */
        foreach ($this->injectedServices as $injectedService) {
            $injectedServiceMock = MockCreator::mock($injectedService->serviceClass(), $injectedService->inject());
            $this->inject($injectedService->serviceName(), $injectedServiceMock);
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
            $this->inject(ServiceEnum::FORM_FACTORY, $formMock);
            // TODO This mock can be removed when FormView is succesfully mocked
            $templatingMock = MockBuilder::createTwigEnvironment();
            $this->inject(ServiceEnum::TEMPLATING, $templatingMock);
        }

        if(!empty($this->injectedForms)) {
            $formMock = MockBuilder::createFormFactoryMock($this->injectedForms);
            // TODO This rendering can be improved, it's only used when mocking forms
            $this->inject(ServiceEnum::FORM_FACTORY, $formMock);
        }
        $templatingMock = MockBuilder::createTwigEnvironment();
        $this->testClient->getContainer()->set(ServiceEnum::TWIG, $templatingMock);

        // Logged in status mock
        if($this->loginData != null) {
            $mockUser = $this->loginData->getUser();
            $token = new PostAuthenticationGuardToken($mockUser, "dev", $mockUser->getRoles());
            foreach ($this->loginData->getTokenAttributes() as $attribute => $value) {
                $token->setAttribute($attribute, $value);
            }

            $mockTokenStorage = MockBuilder::createTokenStorageMock($token);
            $this->inject(ServiceEnum::SECURITY_TOKEN_STORAGE, $mockTokenStorage);
        }

        // Login form mock
        if($this->shouldFailFormLogin || ($this->authException != null)) {
            if($this->authException == null) {
                $this->authException = new MetronomeAuthenticationException("Invalid Credentials");
            }
            $authMock = MockBuilder::createAuthUtilsMock($this->authException);
            $this->inject(ServiceEnum::SECURITY_AUTH_UTILS, $authMock);
        }

        $sessionMock = MockCreator::mock("Symfony\Component\HttpFoundation\Session\Session", array(
            "start" => null,
            "set" => null,
            "save" => null,
            "getId" => "sessionId",
            "getName" => "sessionName",
            "getUsageIndex" => 1,
            "get" => "",
            "remove" => null,
            "getFlashBag" => new FlashBag("someKey")
        ));
        $this->inject("session", $sessionMock);

        if($this->parameterBag != null) {
            $this->inject("parameter_bag", $this->parameterBag);
        }

        if($this->preparedController != null) {
            $controller = $this->prepareController($this->preparedController);
            $this->inject($this->preparedController->getControllerClassName(), $controller);
        }

        $env = new MetronomeEnvironment($this->testClient);
        return $env;
    }

    private function prepareController(PreparedController $preparedController) {
        $defNames = array();
        /** @var MetronomeArgument $definition */
        foreach($preparedController->getControllerArguments() as $definition) {
            if($definition instanceof MetronomeArgument == false) {
                throw new \InvalidArgumentException("Argument must be of type MetronomeArgument");
            }
            $defNames[$definition->getParameterName()] = $definition->getInjectedServiceId();
        }

        try {
            $reflectionController = new \ReflectionClass($preparedController->getControllerClassName());
            $reflectionConstructor = $reflectionController->getConstructor();
            $parameters = $reflectionConstructor->getParameters();

            $arguments = array();
            foreach($parameters as $parameter) {

                if(array_key_exists($parameter->name, $defNames)) {
                    $def = $defNames[$parameter->name];
                    $arguments[$parameter->name] = $this->testClient->getContainer()->get($def);
                } else {
                    throw new \InvalidArgumentException(sprintf("Please provide parameter '%s'", $parameter->name));
                }
            }

            $controllerInstance = $reflectionController->newInstanceArgs($arguments);
            return $controllerInstance;
        } catch (\ReflectionException $e) {
            var_dump($e);
        }
        return null;
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
        $mockMR = MockCreator::mock('Doctrine\Common\Persistence\ManagerRegistry', array(
            "getManagerForClass" => $mockEM,
        ));
        return $mockMR;
    }

    private function verifyState()
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

    private function inject($serviceId, $mock) {
        $this->testClient->getContainer()->set($serviceId, $mock);
    }
}