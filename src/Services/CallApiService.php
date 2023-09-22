<?php

namespace App\Services;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallApiService
{
    private $client;
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $method
     * @param string $uri
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getApi(string $method, string $uri):array {
        $response = $this->client->request(
            $method,
            $uri
        );

        return $response->toArray();
    }

    public function getBeerFromName(string $method, string $uri, string $beer_name):array {
        $response = $this->client->request(
            $method,
            $uri,
            [
                'query' => [
                    'beer_name' => $beer_name,
                ]
            ]
        );

        return $response->toArray();
    }

}