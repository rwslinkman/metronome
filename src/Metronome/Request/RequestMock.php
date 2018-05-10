<?php
namespace Metronome\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class RequestMock
 * @package Metronome\Request
 */
class RequestMock
{
    /**
     * This function creates a Request mock and additionally adds form data
     * @param array $loginFormData
     * @param string $getPathInfo
     * @return \Mockery\MockInterface|Request
     */
    public static function createRequest($loginFormData = array(), $getPathInfo = "/login")
    {
        $sessionMock = \Mockery::mock('Symfony\Component\HttpFoundation\Session\Storage\MetadataBag\Session', array(
            "set" => null
        ));

        $formBag = new ParameterBag($loginFormData);
        $mockRequest = \Mockery::mock('\Symfony\Component\HttpFoundation\Request', array(
                "getPathInfo" => $getPathInfo,
                "isMethod" => true,
                "getSession" => $sessionMock
            )
        );

        $mockRequest->request = $formBag;
        return $mockRequest;
    }
}