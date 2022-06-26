<?php
namespace Metronome;

use Metronome\Tests\Util\SymfonyClient;
use PHPUnit\Framework\TestCase;

class MetronomeBuilderTest extends TestCase
{
    /** @var MetronomeBuilder */
    private $builder;

    public function setUp(): void
    {
        parent::setUp();
        $client = new MetronomeTestClientBuilder();
        $this->builder = new MetronomeBuilder($client->build());
    }

    public function test_givenBuilder_whenConstruct_thenShouldNotCrash() {
        // Mainly to assert during tests that class references are correct
        $this->assertNotNull($this->builder);
    }
}
