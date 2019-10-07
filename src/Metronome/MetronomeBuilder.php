<?php
namespace Metronome;

use \InvalidArgumentException;
use Metronome\Auth\MetronomeAuthenticationException;
use Metronome\Auth\MetronomeLoginData;
use Metronome\Form\MetronomeFormData;
use Metronome\Injection\Argument\MetronomeArgument;
use Metronome\Injection\Argument\MetronomeServiceArgument;
use Metronome\Injection\MetronomeSession;
use Metronome\Injection\Mocking\MetronomeDynamicMockBuilder;
use Metronome\Injection\Mocking\MockBuilder;
use Metronome\Injection\Mocking\MockCreator;
use Metronome\Injection\PreparedController;
use Metronome\Injection\ServiceInjector;
use Metronome\Util\ServiceEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
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
    /** @var KernelBrowser */
    private $testClient;
    /** @var MetronomeLoginData */
    private $loginData;
    /** @var array */
    private $injections;
    /** @var AuthenticationException */
    private $authException;
    /** @var array|MetronomeFormData[] */
    private $injectedForms;
    /** @var PreparedController */
    private $preparedController;
    /** @var MetronomeSession */
    private $injectedSession;

    public function __construct(KernelBrowser $client = null, $useHTTPS = true) {
        if($client != null){
            // Force HTTPS in test
            $client->setServerParameter("HTTPS", $useHTTPS);
        }
        $this->testClient = $client;
        $this->loginData = null;
        $this->injectedForms = array();
        $this->injections = array();
        $this->authException = null;
        $this->preparedController = null;
    }

    public function injectService(ServiceInjector $serviceInjector) {
        $injectedServiceMock = MockCreator::mock($serviceInjector->serviceClass(), $serviceInjector->inject());
        $this->injectObject($serviceInjector->serviceName(), $injectedServiceMock);
    }

    public function injectNamedService($serviceName, ServiceInjector $serviceInjector) {
        $injectedServiceMock = MockCreator::mock($serviceInjector->serviceClass(), $serviceInjector->inject());
        $this->injectObject($serviceName, $injectedServiceMock);
    }

    public function injectDynamicMock($serviceName, MetronomeDynamicMockBuilder $mockBuilder) {
        $injectedMock = $mockBuilder->build();
        $this->injectObject($serviceName, $injectedMock);
    }

    public function injectObject($serviceName, $anObject) {
        $this->injections[$serviceName] = $anObject;
    }

    public function injectSession(MetronomeSession $session = null) {
        if($session == null) {
            $session = new MetronomeSession();
        }
        $this->injectedSession = $session;
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
     * Allows to inject mocked form to use in your Controller tests
     * When calling this multiple times with different MetronomeFormData, they will be returned in the order they were injected.
     * @param MetronomeFormData $formData
     */
    public function injectForm(MetronomeFormData $formData) {
        array_push($this->injectedForms, $formData);
    }

    public function setupController($controllerClass, $parameterDefinitions = array()) {
        $this->preparedController = new PreparedController($controllerClass, $parameterDefinitions);
    }

    /**
     * @return MetronomeEnvironment
     */
    public function build() {
        $this->verifyState();
        $this->testClient->getKernel()->boot();

        $dmBuilder = new MetronomeDoctrineMockBuilder();
        $emMock = $dmBuilder->buildEntityManager(null);
        // Database / Doctrine mock
        $this->inject(ServiceEnum::ENTITY_MANAGER, $emMock);
        $this->inject(ServiceEnum::DEFAULT_ENTITY_MANAGER, $emMock);

        foreach($this->injections as $serviceName => $injection) {
            $this->inject($serviceName, $injection);
        }

        if(!empty($this->injectedForms)) {
            $formMock = MockBuilder::createFormFactoryMock($this->injectedForms);
            // TODO This rendering can be improved, it's only used when mocking forms
            $this->inject(ServiceEnum::FORM_FACTORY, $formMock);
        }
        $templatingMock = MockBuilder::createTwigEnvironment();
        $this->inject(ServiceEnum::TWIG, $templatingMock);

        // Logged in status mock
        $token = null;
        if($this->loginData != null) {
            $mockUser = $this->loginData->getUser();
            $token = new PostAuthenticationGuardToken($mockUser, "dev", $mockUser->getRoles());
            foreach ($this->loginData->getTokenAttributes() as $attribute => $value) {
                $token->setAttribute($attribute, $value);
            }
        }
        $mockTokenStorage = MockBuilder::createTokenStorageMock($token);
        $this->inject(ServiceEnum::SECURITY_TOKEN_STORAGE, $mockTokenStorage);

        // Login form mock
        if($this->authException != null) {
            if($this->authException == null) {
                $this->authException = new MetronomeAuthenticationException("Invalid Credentials");
            }
            $authMock = MockBuilder::createAuthUtilsMock($this->authException);
            $this->inject(ServiceEnum::SECURITY_AUTH_UTILS, $authMock);
        }

        if($this->injectedSession != null) {
            $this->inject(ServiceEnum::SESSION, $this->injectedSession);
        }

        if($this->preparedController != null) {
            $controller = $this->prepareController($this->preparedController);
            $controller->setContainer($this->testClient->getContainer());
            $this->inject($this->preparedController->getControllerClassName(), $controller);
        }

        $env = new MetronomeEnvironment($this->testClient);
        return $env;
    }

    /**
     * @param PreparedController $preparedController
     * @return AbstractController|null
     */
    private function prepareController(PreparedController $preparedController) {
        $argumentObjects = array();
        /** @var MetronomeArgument $definition */
        foreach($preparedController->getControllerArguments() as $definition) {
            if($definition instanceof MetronomeArgument == false) {
                throw new \InvalidArgumentException("Argument must be of type MetronomeArgument");
            }

            $argument = $definition->getInjectedArgument();
            if($definition instanceof MetronomeServiceArgument) {
                $argument = $this->testClient->getContainer()->get($definition->getServiceId());
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
            // TODO: Improve error handling if needed
            var_dump($e);
        }
        return null;
    }

    private function verifyState()
    {
        if ($this->authException && $this->loginData) {
            throw new InvalidArgumentException("Cannot use injectAuthenticationError() and requiresLogin() simultaneously");
        }
    }

    private function inject($serviceId, $mock) {
        $this->testClient->getContainer()->set($serviceId, $mock);
    }
}