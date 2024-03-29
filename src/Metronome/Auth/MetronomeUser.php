<?php
namespace Metronome\Auth;

use Symfony\Component\Security\Core\User\UserInterface;

class MetronomeUser implements UserInterface
{
    private $username;
    private $password;
    private $salt;
    private $roles;
    private $userId;

    /**
     * MetronomeUser constructor.
     * @param string $username
     * @param string $password
     * @param string $salt
     * @param array $roles
     */
    public function __construct($username = "", $password = "", $salt = "", $roles = array(), $userId = "")
    {
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->roles = $roles;
        $this->userId = $userId;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->password = "";
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @param string $userId
     * @return void
     */
    public function setUserIdentifier(string $userId): void
    {
        $this->userId = $userId;
    }
}