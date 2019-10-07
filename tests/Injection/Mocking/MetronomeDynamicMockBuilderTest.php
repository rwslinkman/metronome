<?php
namespace Metronome\Injection\Mocking;

use Metronome\Tests\Util\DummyClass;
use PHPUnit\Framework\TestCase;

class MetronomeDynamicMockBuilderTest extends TestCase
{
    private $builder;

    public function setUp()
    {
        parent::setUp();
        $this->builder = new MetronomeDynamicMockBuilder(DummyClass::class);
    }

    public function test_givenNoMethods_whenBuild_thenShouldReturnObject() {
        $result = $this->builder->build();
        $this->assertNotNull($result);
    }

    /** @expectedException \Mockery\Exception\BadMethodCallException */
    public function test_givenNoMethods_whenBuild_thenShouldThrowException() {
        /** @var DummyClass $mock */
        $mock = $this->builder->build();

        $mock->giveSomeInteger();
    }

    public function test_givenIntegerMethodMocked_whenBuild_thenShouldReturnObject() {
        $this->builder->method("giveSomeInteger", 42);

        /** @var DummyClass $mock */
        $result = $this->builder->build();

        $this->assertNotNull($result);
    }

    public function test_givenIntegerMethodMocked_whenBuild_thenShouldReturnCorrectValue() {
        $this->builder->method("giveSomeInteger", 42);
        /** @var DummyClass $mock */
        $mock = $this->builder->build();

        $result = $mock->giveSomeInteger();
        $this->assertEquals(42, $result);
        $this->assertNotEquals(DummyClass::SOME_INTEGER, $result);
    }

    /** @expectedException \Mockery\Exception\BadMethodCallException */
    public function test_givenIntegerMethodMocked_whenCallNonMocked_thenShouldThrowException() {
        $this->builder->method("giveSomeInteger", 42);
        /** @var DummyClass $mock */
        $mock = $this->builder->build();

        $mock->giveSomeString();
    }

    public function test_givenAllMocked_whenBuild_thenShouldReturnObject() {
        $this->builder->method("giveSomeInteger", 42);
        $this->builder->method("giveSomeString", "helloWorld");

        /** @var DummyClass $mock */
        $result = $this->builder->build();

        $this->assertNotNull($result);
    }

    public function test_givenAllMocked_whenBuild_thenShouldReturnCorrectValue() {
        $this->builder->method("giveSomeInteger", 42);
        $this->builder->method("giveSomeString", "helloWorld");
        /** @var DummyClass $mock */
        $mock = $this->builder->build();

        $integerResult = $mock->giveSomeInteger();
        $this->assertEquals(42, $integerResult);
        $this->assertNotEquals(DummyClass::SOME_INTEGER, $integerResult);

        $stringResult = $mock->giveSomeString();
        $this->assertEquals("helloWorld", $stringResult);
        $this->assertNotEquals(DummyClass::SOME_STRING, $stringResult);
    }
}
