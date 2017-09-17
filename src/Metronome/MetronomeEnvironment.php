<?php
namespace Metronome;

use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Routing\RouterInterface;

/**
 */
class MetronomeEnvironment
{
    const HEADER_PREFIX = "HTTP_";
    private $client;

    /* package */ function __construct(Client $c)
    {
        $this->client = $c;
    }

    /**
     * @param String $serviceName
     * @param mixed $mock
     */
    /* package */ function injectService($serviceName, $mock) {
        if($this->client != null) {
            $this->client->getContainer()->set($serviceName, $mock);
        }
    }

    /**
     * @param $uri
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function get($uri) {
        return $this->request("GET", $uri);
    }

    /**
     * @param string $uri
     * @param array|string $body
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postJson($uri, $body) {
        $headers = array(
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT'       => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        );
        return $this->request("POST", $uri, array(), $headers, $body);
    }

    /**

     * @param string $uri
     * @param array $fileData
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postFiles($uri, $fileData, $headers = array()) {
        $this->validateHeaders($headers);
        return $this->request("POST", $uri, $fileData, $headers);
    }

    public function putJson($uri, $body) {
        $headers = array(
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT'       => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        );
        return $this->request("PUT", $uri, array(), $headers, $body);
    }

    /**
     * @return array
     */
    public function getFlashBag() {
        return $this->client->getContainer()->get("session")->getFlashBag()->all();
    }

    /**
     * @return \Mockery\MockInterface|RouterInterface
     */
    public static function createRouterMock() {
        $routerMock = \Mockery::mock('\Symfony\Component\Routing\Router', array(
            "getRouteCollection" => array(),
            "generate" => "http://some/url"
        ));
        return $routerMock;
    }

    /**
     * @return Client
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * @param $method
     * @param string $uri
     * @param array $files
     * @param array $headers
     * @param string $body
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function request($method, $uri, $files = array(), $headers = array(), $body = null) {
        // Prepare JSON body
        $content = null;
        if($body != null){
            $content = json_encode($body);
        }

        $this->client->request($method, $uri, array(), $files, $headers, $content);
        // TODO: Analyze response and warn for errors, flashbag messages, etc.
        return $this->client->getResponse();
    }

    /**
     * @param string[] $headers List of headers to validate
     * @throws InvalidArgumentException In case one of the headers does not start with "HTTP_"
     */
    private function validateHeaders($headers) {
        foreach($headers as $headerName => $headerValue) {
            // Check each header for valid start characters.
            if(substr($headerName, 0, 5 ) !== self::HEADER_PREFIX) {
                $errorStr = sprintf("Header '%s' does not start with 'HTTP_'", $headerName);
                throw new InvalidArgumentException($errorStr);
            }
        }
    }
}
