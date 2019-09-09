<?php
namespace Metronome\Tests;

use Metronome\MetronomeEnvironment;
use Metronome\MetronomeTestClientBuilder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MetronomeEnvironmentTest extends WebTestCase
{
    public function test_example() {
        $browser = (new MetronomeTestClientBuilder())->build();
        $env = new MetronomeEnvironment($browser);
        $this->assertNotNull($env);
    }
}
