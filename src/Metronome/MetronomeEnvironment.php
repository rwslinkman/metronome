<?php
namespace Metronome;

use InvalidArgumentException;
use Metronome\Injection\MockCreator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\BrowserKit\Cookie;

/**
 * class MetronomeEnvironment
 * Environment to perform HTTP requests under tests
 * Can be created using the MetronomeBuilder
 */
class MetronomeEnvironment
{
    const HEADER_PREFIX = "HTTP_";
    /** @var KernelBrowser */
    private $client;
    /** @var Crawler */
    private $latestCrawler;

    /* package */
    function __construct(KernelBrowser $c)
    {
        $this->client = $c;
    }

    /**
     * Sends a GET request to the MetronomeEnvironment to test your application
     * @param string $uri
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function get($uri, $headers = array(), $sessionMap = array())
    {
        $this->validateHeaders($headers);
        return $this->request("GET", $uri, array(), $headers, $sessionMap);
    }

    /**
     * Sends a POST request to the MetronomeEnvironment to test your application
     * @param string $uri
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function post($uri, $headers = array(), $sessionMap = array()) {
        $this->validateHeaders($headers);
        return $this->request("POST", $uri, array(), $headers, $sessionMap);
    }

    /**
     * @param string $uri
     * @param array|string $body
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postJson($uri, $body, $headers = array())
    {

        $headers['HTTP_CONTENT_TYPE']       = 'application/json';
        $headers['HTTP_ACCEPT']             = 'application/json';
        $headers['HTTP_X-Requested-With']   = 'XMLHttpRequest';
        return $this->request("POST", $uri, array(), $headers, $body);
    }

    /**
     * @param string $uri
     * @param array $fileData
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postFiles($uri, $fileData, $headers = array())
    {
        $this->validateHeaders($headers);
        return $this->request("POST", $uri, $fileData, $headers);
    }

    /**
     * Send POST request to specified $uri with given $formData.
     * $formData must be of array type, containing an array for each form posted
     *
     * Example:[
     *  myform[firstname]
     *  myform[lastname]
     *  otherform[question1]
     *  otherform[question2]
     * ]
     * @param $uri
     * @param $formId string - HTML selector to find the specific form
     * @param array $formData
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postForm($uri, $formId, $formData = array()) {
        $crawler = $this->client->request('GET', $uri);
        $form = $crawler->filter('#'.$formId)->form();
        $form->setValues($formData);

        $this->client->setServerParameter("HTTP_X-Requested-With" , "XMLHttpRequest");
        $this->client->submit($form);

        return $this->client->getResponse();
    }

    /**
     * @param $uri
     * @param $body
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putJson($uri, $body, $headers = array())
    {
        $headers['HTTP_CONTENT_TYPE']       = 'application/json';
        $headers['HTTP_ACCEPT']             = 'application/json';
        $headers['HTTP_X-Requested-With']   = 'XMLHttpRequest';
        return $this->request("PUT", $uri, array(), $headers, $body);
    }

    /**
     * @return array
     */
    public function getFlashBag()
    {
        return $this->client->getContainer()->get("session")->getFlashBag()->all();
    }

    /**
     * @return \Mockery\MockInterface|RouterInterface
     */
    public static function createRouterMock()
    {
        $routerMock = MockCreator::mock('\Symfony\Component\Routing\Router', array(
            "getRouteCollection" => array(),
            "generate" => "http://some/url"
        ));
        return $routerMock;
    }

    /**
     * @return KernelBrowser
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return Crawler
     */
    public function getLatestCrawler()
    {
        return $this->latestCrawler;
    }

    /**
     * @param $method
     * @param string $uri
     * @param array $files
     * @param array $headers
     * @param string $body
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function request($method, $uri, $files = array(), $headers = array(), $body = null, $sessionMap = array())
    {
        // Prepare JSON body
        $content = null;
        if ($body != null) {
            $content = json_encode($body);
        }

//        $this->prepareSession($sessionMap);

        $this->latestCrawler = $this->client->request($method, $uri, array(), $files, $headers, $content);
        // TODO: Analyze response and warn for errors, flashbag messages, etc.
        return $this->client->getResponse();
    }

    /**
     * @param string[] $headers List of headers to validate
     * @throws InvalidArgumentException In case one of the headers does not start with "HTTP_"
     */
    private function validateHeaders($headers)
    {
        foreach ($headers as $headerName => $headerValue) {
            // Check each header for valid start characters.
            if (substr($headerName, 0, 5) !== self::HEADER_PREFIX) {
                $errorStr = sprintf("Header '%s' does not start with '%s'", $headerName, self::HEADER_PREFIX);
                throw new InvalidArgumentException($errorStr);
            }
        }
    }

    private function prepareSession($sessionMap) {
        $session = $this->client->getContainer()->get('session');
        $session->start();
        foreach($sessionMap as $sessionKey => $sessionValue) {
            $session->set($sessionKey, $sessionValue);
        }
        $session->save();
        $this->client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
    }
}
