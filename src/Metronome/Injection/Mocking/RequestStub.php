<?php
namespace Metronome\Injection\Mocking;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestStub
 * @package Metronome\Util
 */
class RequestStub extends Request
{
    private $mockPath;

    /**
     * RequestStub constructor.
     * @param string $path
     * @param array $headers
     */
    public function __construct($path, $headers) {
        parent::__construct(array(), array(), array(), array(), array(), $headers);
        $this->mockPath = $path;
    }


    public function getPathInfo(): string
    {
        return $this->mockPath;
    }
}