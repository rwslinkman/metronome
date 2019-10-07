<?php
namespace Metronome\Tests\Util;


class DummyClass
{
    const SOME_INTEGER = 1337;
    const SOME_STRING = "leet";

    public function giveSomeInteger() {
        return self::SOME_INTEGER;
    }

    public function giveSomeString() {
        return self::SOME_STRING;
    }

    public function doSomeStuff() {
        // void method
    }
}