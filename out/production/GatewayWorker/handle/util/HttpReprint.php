<?php

namespace handle\util;

use GuzzleHttp\Client;

class HttpReprint
{
    private static $instance;

    /** @var Client */
    private $httpClient;

    private $token;

    private function __construct($token)
    {
        $this->token = $token;
        $this->httpClient = new Client([
            'verify' => false,
            'headers' => [
                "content-type" => "application/x-www-form-urlencoded",
                "connection" => "keep-alive",
                "accept-encoding" => "gzip, deflate, br",
                "Accept" => "*/*",
                "authorization"=> "Bearer {$token}"
            ]
        ]);
    }

    /**
     * @return self
     */
    public static function getInstance($token)
    {
        if (empty(self::$instance)) {
            self::$instance = new self($token);
        }
        return self::$instance;
    }

    /**
     * @param $url
     * @param $query
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($url, $query)
    {
        $response = $this->httpClient->request("GET", $url, [
            'query' => $query
        ]);

        if ($response->getStatusCode() === 200) {
            $body = $response->getBody();
            return $body->getContents();
        }
        return null;
    }

    /**
     * @param $url
     * @param $params
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post($url, $params = null)
    {
        $response = $this->httpClient->request("POST", $url, [
            'body' => $params
        ]);

        if ($response->getStatusCode() === 200) {
            $body = $response->getBody();
            return $body->getContents();
        }
        return null;
    }
}