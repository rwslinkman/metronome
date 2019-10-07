<?php
namespace Metronome\Injection;

use PHPUnit\Framework\TestCase;

class MetronomeSessionTest extends TestCase
{
    private $session;

    public function setUp() {
        parent::setUp();
        $this->session = new MetronomeSession();
    }

    public function test_givenSetAttribute_whenGet_thenShouldReturnCorrectValue() {
        $this->session->set("someKey", "someValue");

        $result = $this->session->get("someKey");

        $this->assertEquals("someValue", $result);
    }

    public function test_givenSetAttribute_whenGet_otherAttribute_thenShouldReturnNull() {
        $this->session->set("someKey", "someValue");

        $result = $this->session->get("otherKey");

        $this->assertNull($result);
    }

    public function test_givenSetAttribute_whenGetDefault_otherAttribute_thenShouldReturnNull() {
        $this->session->set("someKey", "someValue");

        $result = $this->session->get("otherKey", "someDefault");

        $this->assertEquals("someDefault", $result);
    }
}
