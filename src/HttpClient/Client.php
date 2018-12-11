<?php

namespace SeoAnalyzer\HttpClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use SeoAnalyzer\HttpClient\Exception\HttpException;

class Client implements ClientInterface
{
    /**
     * @inheritdoc
     */
    public function get(string $url, array $options = []): ResponseInterface
    {
        try {
            return (new GuzzleClient())->request('GET', $url, $options);
        } catch (GuzzleException $e) {
            throw new HttpException('Error getting url: ' . $e->getMessage(), $e->getCode(), $e);        }
    }
}
