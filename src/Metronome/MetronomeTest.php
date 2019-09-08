<?php
namespace Metronome;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MetronomeTest extends WebTestCase
{
    protected static function createClient(array $options = [], array $server = [])
    {
        $builder = new MetronomeTestClientBuilder();
        return $builder->build();
    }
}