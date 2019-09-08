<?php
namespace Metronome;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class MetronomeTestClientBuilder
{
    public static function build() {
        $kernel = new MetronomeTestKernel();
        return new KernelBrowser($kernel);
    }
}