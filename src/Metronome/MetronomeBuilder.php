<?php
namespace Metronome;

use \InvalidArgumentException;
use Metronome\Form\MetronomeFormData;
use Metronome\Injection\MetronomeLoginData;
use Metronome\Injection\MockBuilder;
use Metronome\Injection\MockCreator;
use Metronome\Util\MetronomeAuthenticationException;
use Metronome\Util\ServiceEnum;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
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
    /** @var MetronomeLoginData */
    private $loginData;
    /** @var AuthenticationException */
    private $authException;
    /** @var array|MetronomeFormData[] */
    private $injectedForms;

    public function __construct(KernelBrowser $client = null, $useHTTPS = true) {
        if($client != null){
            // Force HTTPS in test
            $client->setServerParameter("HTTPS", $useHTTPS);
        }
        $this->testClient = $client;
        $this->loginData = null;
        $this->injectedForms = array();
        $this->authException = null;
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

    /**
     * @return MetronomeEnvironment
     */
    public function build() {
        $this->verifyState();

        $doctrineMockBuilder = new MetronomeDoctrineMockBuilder();
        $emMock = $doctrineMockBuilder->buildEntityManager(null);
        // Database / Doctrine mock
        $this->testClient->getKernel()->boot();
        $this->inject(ServiceEnum::ENTITY_MANAGER, $emMock);
        $this->inject("doctrine.orm.default_entity_manager", $emMock);


        if(!empty($this->injectedForms)) {
            $formMock = MockBuilder::createFormFactoryMock($this->injectedForms);
            // TODO This rendering can be improved, it's only used when mocking forms
            $this->inject(ServiceEnum::FORM_FACTORY, $formMock);
        }
        $twigMock = MockBuilder::createTwigEnvironment();
        $this->inject(ServiceEnum::TWIG, $twigMock);

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
        if($this->authException != null) {
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

        $this->inject("tokenStorage", MockBuilder::createTokenStorageMock());

        $env = new MetronomeEnvironment($this->testClient);
        return $env;
    }

    private function verifyState() {
        if ($this->authException && $this->loginData) {
            throw new InvalidArgumentException("Cannot use injectAuthenticationError() and requiresLogin() simultaneously");
        }
    }

    private function inject($serviceId, $mock) {
        $this->testClient->getContainer()->set($serviceId, $mock);
    }
}