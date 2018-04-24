<?php
namespace Metronome;

use Metronome\Tests\Util\SymfonyClient;

class MetronomeBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $builder;

    public function setUp()
    {
        parent::setUp();
        $client = SymfonyClient::mock();
        $this->builder = new MetronomeBuilder($client);
    }

    public function test_givenBuilder_whenConstruct_thenShouldNotCrash() {
        // Mainly to assert during tests that class references are correct
        $this->assertNotNull($this->builder);
    }
}
