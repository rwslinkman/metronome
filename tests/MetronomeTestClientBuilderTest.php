<?php
namespace Metronome;

use Metronome\Injection\MetronomeDefinition;
use PHPUnit\Framework\TestCase;

class MetronomeTestClientBuilderTest extends TestCase
{
    /** @var MetronomeTestClientBuilder */
    private $builder;

    public function setUp()
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
