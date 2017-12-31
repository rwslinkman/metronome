<?php
namespace Metronome;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MetronomeEnvironmentTest extends WebTestCase
{
    public function test_example() {
        /** @var Client $clientMock */
        $clientMock = \Mockery::mock('\Symfony\Bundle\FrameworkBundle\Client', array(
            'getContainer' => array()
        ));
        $env = new MetronomeEnvironment($clientMock);
        $this->assertNotNull($env);
    }
}
