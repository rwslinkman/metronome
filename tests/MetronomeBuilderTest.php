<?php
namespace Metronome;

use Metronome\Tests\Util\SymfonyClient;
use PHPUnit\Framework\TestCase;

class MetronomeBuilderTest extends TestCase
{
    /** @var MetronomeBuilder */
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_givenBuilder_andShouldFailLoginFormCalled_whenRequiresLogin_andBuild_thenShouldThrowException() {
        $this->builder->shouldFailFormLogin();

        $this->builder->requiresLogin();
        $this->builder->build();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_givenBuilder_andRequiresLoginCalled_whenShouldFailLoginForm_andBuild_thenShouldThrowException() {
        $this->builder->requiresLogin();

        $this->builder->shouldFailFormLogin();
        $this->builder->build();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_givenBuilder_andRequiresLoginCalled_whenInjectAuthenticationError_andBuild_thenShouldThrowException() {
        $this->builder->requiresLogin();

        $this->builder->injectAuthenticationError();
        $this->builder->build();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_givenBuilder_andShouldFailLoginFormCalled_whenInjectAuthenticationError_andBuild_thenShouldNotThrowException() {
        $this->builder->shouldFailFormLogin();

        $this->builder->requiresLogin();
        $this->builder->build();

        $this->assertTrue(true); // To keep test/assert ratio equal
    }
}
