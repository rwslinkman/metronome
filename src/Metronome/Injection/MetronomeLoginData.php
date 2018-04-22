<?php
namespace Metronome\Injection;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class MetronomeLoginData
 * @package Metronome\Data
 * @codeCoverageIgnore
 */
class MetronomeLoginData
{
    private $user;
    private $authenticatorService;
    private $attributes;

    /**
     * MetronomeLoginData constructor.
     * @param $user
     * @param $authenticatorService
     */
    public function __construct(UserInterface $user, $authenticatorService)
    {
        $this->user = $user;
        $this->authenticatorService = $authenticatorService;
        $this->attributes = array();
    }

    public static function defaultLoginData() {
        return new self(new MetronomeUser(), "app.token_authenticator");
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getAuthenticatorService()
    {
        return $this->authenticatorService;
    }

    /**
     * @param mixed $authenticatorService
     */
    public function setAuthenticatorService($authenticatorService)
    {
        $this->authenticatorService = $authenticatorService;
    }

    /**
     * @return array
     */
    public function getTokenAttributes() {
        return $this->attributes;
    }

    /**
     * @param $attributes
     */
    public function setTokenAttributes($attributes) {
        $this->attributes = $attributes;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function addTokenAttribute($name, $value) {
        $this->attributes[$name] = $value;
    }
}