<?php
namespace Metronome\Tests\File;

use Metronome\File\MetronomeTestFile;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertNotNull;

class MetronomeTestFileTest extends TestCase
{
    public function test_canBeConstructed() {
        $result = new MetronomeTestFile("someName.xml");
        assertNotNull($result);
    }
}