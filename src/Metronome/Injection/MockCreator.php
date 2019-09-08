<?php
namespace Metronome\Injection;

class MockCreator
{
    public static function mock($className, $injection) {
        return \Mockery::mock($className, $injection);
    }
}