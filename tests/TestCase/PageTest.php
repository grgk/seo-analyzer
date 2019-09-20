<?php
namespace Tests\TestCase;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use SeoAnalyzer\Factor;
use SeoAnalyzer\HttpClient\ClientInterface;
use SeoAnalyzer\Page;
use Tests\TestCase;

class PageTest extends TestCase
{
    public function testConstructor()
    {
        $url = 'https://www.example.org';
        $html = '<html lang="en"><body><p>testing</p></body></html>';
        $clientMock = $this->getClientMock($html);
        $page = new Page($url, 'en_GB', $clientMock);
        $this->assertInstanceOf(Page::class, $page);
        $this->assertEquals($html, $page->content);
        $this->assertEquals($url, $page->url);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetMetricsPassWithUrl()
    {
        $html = '<html lang="en"><body><p>testing</p></body></html>';
        $clientMock = $this->getClientMock($html);
        $page = new Page(null, null, $clientMock);
        $page->url = 'http://www.example.org';
        $metrics = $page->getMetrics();
        $this->assertIsArray($metrics);
    }

    public function testSetFactor()
    {
        $page = new Page();
        $page->setFactor('level', 'example');
        $page->setFactor('level_A.level_B', 'example_AB');
        $page->setFactor('level1.level2.level3', 'example123');
        $this->assertEquals($page->factors['level'], 'example');
        $this->assertEquals($page->factors['level_A']['level_B'], 'example_AB');
        $this->assertEquals($page->factors['level1']['level2']['level3'], 'example123');
    }

    public function testSSLFactor()
    {
        $stream = $this->getMockBuilder(StreamInterface::class)->disableOriginalConstructor()->getMock();
        $stream->method('getContents')->willReturn([]);
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->method('getBody')->willReturn($stream);
        $response->method('getHeader')->with('X-Guzzle-Redirect-History')->willReturn(['redirect' => 'redirect']);
        $clientMock = $this->getMockBuilder(ClientInterface::class)->getMock();
        $clientMock->method('get')->with(['http://www.example.org'])->willReturn($response);
        $response->method('getStatusCode')->willReturn(400);
        $clientMock->method('get')->with(['https://www.example.org'])->willReturn('');

        $page = new Page('http://www.example.org');
        $page->getContent();
        $this->assertTrue($page->getFactor(Factor::SSL));
    }
}
