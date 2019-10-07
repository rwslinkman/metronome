<?php
namespace Metronome\Tests\Util;

use Metronome\Injection\Mocking\MockCreator;
use Symfony\Bundle\FrameworkBundle\Client;

abstract class SymfonyClient
{
    /**
     * @return Client|\Mockery\MockInterface
     */
    public static function mock() {
        $clientMock = MockCreator::mock('\Symfony\Bundle\FrameworkBundle\Client', array(
            'getContainer' => array(),
            'setServerParameter' => null
        ));
        return $clientMock;
    }
}