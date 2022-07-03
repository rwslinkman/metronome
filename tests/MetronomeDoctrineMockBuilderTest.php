<?php
namespace Metronome\Tests;

use Metronome\MetronomeDoctrineMockBuilder;
use PHPUnit\Framework\TestCase;

class MetronomeDoctrineMockBuilderTest extends TestCase
{
    public function test_givenBuilder_whenBuildEntityManager_thenShouldReturnObject() {
        $builder = new MetronomeDoctrineMockBuilder();

        $result = $builder->buildEntityManager();

        $this->assertNotNull($result);
    }

    public function test_givenBuilder_whenBuildFixtureReferenceRepoMock_thenShouldReturnObject() {
        $builder = new MetronomeDoctrineMockBuilder();

        $result = $builder->buildFixtureReferenceRepoMock();

        $this->assertNotNull($result);
    }

    public function test_givenBuilder_whenBuildManagerRegistryMock_thenShouldReturnObject() {
        $builder = new MetronomeDoctrineMockBuilder();

        $result = $builder->buildManagerRegistryMock();

        $this->assertNotNull($result);
    }
}
