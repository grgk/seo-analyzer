<?php
namespace Tests\TestCase;

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
}
