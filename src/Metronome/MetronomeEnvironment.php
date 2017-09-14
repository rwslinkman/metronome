<?php
namespace Metronome;

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
        $this->client->request('GET', $uri);
        // TODO: Analyze response and warn for errors, flashbag messages, etc.
        return $this->client->getResponse();
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
        return $this->postRequest($uri, array(), $headers, $body);
    }

    /**
     * TODO Check if all in $headers start with HEADER_PREFIX
     * @param string $uri
     * @param array $fileData
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postFiles($uri, $fileData, $headers = array()) {
        return $this->postRequest($uri, $fileData, $headers);
    }

    /**
     * @return array
     */
    public function getFlashBag() {
        return $this->client->getContainer()->get("session")->getFlashBag()->all();
    }

    /**
     * @param string $uri
     * @param array $files
     * @param array $headers
     * @param string $body
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function postRequest($uri, $files = array(), $headers = array(), $body = null) {
        // Prepare JSON body
        $content = null;
        if($body != null){
            $content = json_encode($body);
        }

        $this->client->request("POST", $uri, array(), $files, $headers, $content);
        // TODO: Analyze response and warn for errors, flashbag messages, etc.
        return $this->client->getResponse();
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
}
