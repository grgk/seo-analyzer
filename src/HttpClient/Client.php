<?php

namespace SeoAnalyzer\HttpClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use SeoAnalyzer\HttpClient\Exception\HttpException;

class Client implements ClientInterface
{
    protected $options = [
        'allow_redirects' => ['track_redirects' => true],
        'headers' => [
            'User-Agent' => 'grgk-seo-analyzer/1.0'
        ]
    ];

    /**
     * @inheritdoc
     */
    public function get(string $url, array $options = []): ResponseInterface
    {
        if (empty($options)) {
            $options = $this->options;
        }
        try {
            return (new GuzzleClient(['verify' => false]))->request('GET', $url, $options);
        } catch (GuzzleException $e) {
            throw new HttpException('Error getting url: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
