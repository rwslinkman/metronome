<?php
namespace Metronome\Injection\Mocking;

class MockCreator
{
    public static function mock($className, $injection) {
        return \Mockery::mock($className, $injection);
    }
}