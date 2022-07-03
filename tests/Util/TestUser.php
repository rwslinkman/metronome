<?php
namespace Metronome\Tests\Util;

use Symfony\Component\Security\Core\User\UserInterface;

class TestUser implements UserInterface
{

    public function getRoles(): array
    {
        return array();
    }

    public function eraseCredentials()
    {
        // NOP
    }

    public function getUserIdentifier(): string
    {
        return "someIdentifier";
    }
}