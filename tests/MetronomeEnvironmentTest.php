<?php
namespace Metronome\Tests;

use Metronome\MetronomeEnvironment;
use Metronome\Tests\Util\SymfonyClient;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MetronomeEnvironmentTest extends WebTestCase
{
    public function test_example() {
        $clientMock = SymfonyClient::mock();
        $env = new MetronomeEnvironment($clientMock);
        $this->assertNotNull($env);
    }
}
