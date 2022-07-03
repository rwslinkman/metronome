<?php
namespace Metronome\Tests;

use Metronome\Injection\MetronomeDefinition;
use Metronome\MetronomeTestClientBuilder;
use PHPUnit\Framework\TestCase;

class MetronomeTestClientBuilderTest extends TestCase
{
    /** @var MetronomeTestClientBuilder */
    private $builder;

    public function setUp(): void
    {
        parent::setUp();
        $this->builder = new MetronomeTestClientBuilder();
    }

    public function test_givenBuilder_whenBuild_thenShouldReturnObject() {
        $result = $this->builder->build();

        $this->assertNotNull($result);
    }

    public function test_givenDefinition_whenBuild_thenShouldReturnObject() {
        $this->builder->addDefinition(new MetronomeDefinition("SomeObject::class"));

        $result = $this->builder->build();

        $this->assertNotNull($result);
    }
}
