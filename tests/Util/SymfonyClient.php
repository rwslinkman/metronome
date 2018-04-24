<?php
namespace Metronome\Tests\Util;

use Symfony\Bundle\FrameworkBundle\Client;

abstract class SymfonyClient
{
    /**
     * @return Client|\Mockery\MockInterface
     */
    public static function mock() {
        $clientMock = \Mockery::mock('\Symfony\Bundle\FrameworkBundle\Client', array(
            'getContainer' => array(),
            'setServerParameter' => null
        ));
        return $clientMock;
    }
}