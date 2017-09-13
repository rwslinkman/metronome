<?php
namespace JappserBundle\Tests;


use Symfony\Component\HttpFoundation\Request;

class TestRequest extends Request
{
    private $mockPath;

    /**
     * TestRequest constructor.
     * @param string $path
     * @param array $headers
     */
    public function __construct($path, $headers) {
        parent::__construct(array(), array(), array(), array(), array(), $headers);
        $this->mockPath = $path;
    }


    public function getPathInfo()
    {
        return $this->mockPath;
    }
}