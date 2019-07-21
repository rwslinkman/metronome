<?php
namespace Metronome;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MetronomeTest extends WebTestCase
{
    protected static function getKernelClass()
    {
        return MetronomeKernel::class;
    }
}