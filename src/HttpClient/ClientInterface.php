<?php

namespace SeoAnalyzer\HttpClient;

use Psr\Http\Message\ResponseInterface;
use SeoAnalyzer\HttpClient\Exception\HttpException;

interface ClientInterface
{
    /**
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     * @throws HttpException
     */
    public function get(string $url, array $options = []): ResponseInterface;
}
