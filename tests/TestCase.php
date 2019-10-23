<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use SeoAnalyzer\Cache;
use SeoAnalyzer\HttpClient\ClientInterface;

abstract class TestCase extends PhpUnitTestCase
{
    public function setUp()
    {
        parent::setUp();
        $cache = new Cache();
        $cache->adapter->clear();
    }

    /**
     * @param string|null $response Response body content to be returned
     * @return MockObject|ClientInterface
     */
    public function getClientMock(string $response = null)
    {
        if (empty($response)) {
            $response = file_get_contents(__DIR__ . '/data/test.html');
        }
        $stream = $this->getMockBuilder(StreamInterface::class)->disableOriginalConstructor()->getMock();
        $stream->method('getContents')->willReturn($response);
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->method('getBody')->willReturn($stream);
        $response->method('getHeader')->with('X-Guzzle-Redirect-History')->willReturn(['redirect' => 'redirect']);
        $clientMock = $this->getMockBuilder(ClientInterface::class)->getMock();
        $clientMock->method('get')->willReturn($response);
        return $clientMock;
    }
}
