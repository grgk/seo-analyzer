<?php
namespace Tests\TestCase;

use SeoAnalyzer\Cache;
use SeoAnalyzer\Page;
use Tests\TestCase;

class PageTest extends TestCase
{
    /**
     * @throws \SeoAnalyzer\HttpClient\Exception\HttpException
     */
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
     * @throws \SeoAnalyzer\HttpClient\Exception\HttpException
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
}
