<?php
namespace Metronome\Tests\Injection\Mocking;

use Metronome\Injection\Mocking\MetronomeDynamicMockBuilder;
use Metronome\Tests\Util\DummyClass;
use Mockery\Exception\BadMethodCallException;
use PHPUnit\Framework\TestCase;

class MetronomeDynamicMockBuilderTest extends TestCase
{
    private $builder;

    public function setUp(): void
    {
        parent::setUp();
        $this->builder = new MetronomeDynamicMockBuilder(DummyClass::class);
    }

    public function test_givenNoMethods_whenBuild_thenShouldReturnObject() {
        $result = $this->builder->build();
        $this->assertNotNull($result);
    }

    public function test_givenNoMethods_whenBuild_thenShouldThrowException() {
        $this->expectException(BadMethodCallException::class);

        /** @var DummyClass $mock */
        $mock = $this->builder->build();

        /** @noinspection PhpExpressionResultUnusedInspection */
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

    public function test_givenIntegerMethodMocked_whenCallNonMocked_thenShouldThrowException() {
        $this->expectException(BadMethodCallException::class);

        $this->builder->method("giveSomeInteger", 42);
        /** @var DummyClass $mock */
        $mock = $this->builder->build();

        /** @noinspection PhpExpressionResultUnusedInspection */
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
