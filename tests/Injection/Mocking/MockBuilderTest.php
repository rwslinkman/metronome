<?php
namespace Metronome\Tests\Injection\Mocking;

use Metronome\Form\MetronomeFormData;
use Metronome\Injection\Mocking\MockBuilder;
use Metronome\Tests\Util\TestUser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

class MockBuilderTest extends TestCase
{
    public function test_createMockEntityManager() {
        $result = MockBuilder::createMockEntityManager();

        $this->assertNotNull($result);
    }

    public function test_createMockUserProvider() {
        $input = new PostAuthenticationToken(new TestUser(), "testFirewall", array());

        $result = MockBuilder::createMockUserProvider($input);

        $this->assertNotNull($result);
    }

    public function test_createAuthUtilsMock() {
        $result = MockBuilder::createAuthUtilsMock(null);

        $this->assertNotNull($result);
    }

    public function test_createFormFactoryMock() {
        $result = MockBuilder::createFormFactoryMock(array(
            new MetronomeFormData()
        ));

        $this->assertNotNull($result);
    }

    public function test_createTwigEnvironment() {
        $result = MockBuilder::createTwigEnvironment();

        $this->assertNotNull($result);
    }

    public function test_createTokenStorageMock() {
        $result = MockBuilder::createTokenStorageMock();

        $this->assertNotNull($result);
    }
}