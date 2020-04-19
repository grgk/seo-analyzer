<?php

namespace Tests\TestCase;

use SeoAnalyzer\Analyzer;
use SeoAnalyzer\HttpClient\Exception\HttpException;
use ReflectionException;
use SeoAnalyzer\Metric\AbstractMetric;
use SeoAnalyzer\Page;
use Tests\TestCase;

class AnalyzerTest extends TestCase
{
    /**
     * @throws HttpException
     * @throws ReflectionException
     */
    public function testAnalyzeUrlPass()
    {
        $clientMock = $this->getClientMock();
        $analyzer = new Analyzer(null, $clientMock);
        $results = $analyzer->analyzeUrl('http://www.example.org');
        $this->assertTrue(is_array($results));
        $this->assertEquals(count($analyzer->getMetrics()), count($results));
        $this->assertContains('You should avoid redirects', $results['PageRedirect']['analysis']);
        $this->assertArrayHasKey('analysis', current($results));
        $this->assertArrayHasKey('name', current($results));
        $this->assertArrayHasKey('description', current($results));
        $this->assertArrayHasKey('value', current($results));
        $this->assertArrayHasKey('negative_impact', current($results));
    }

    /**
     * @throws HttpException
     * @throws ReflectionException
     */
    public function testAnalyzeUrlPassWithKeywordTranslated()
    {
        $clientMock = $this->getClientMock();
        $analyzer = new Analyzer(null, $clientMock);
        $results = $analyzer->analyzeUrl('http://www.example.org', 'keyword', 'pl_PL');
        $this->assertTrue(is_array($results));
        $this->assertEquals(count($analyzer->getMetrics()), count($results));
        $this->assertContains('Powinienieś unikać przekierowań', $results['PageRedirect']['analysis']);
    }

    /**
     * @throws HttpException
     * @throws ReflectionException
     */
    public function testAnalyzeUrlWithKeywordPass()
    {
        $clientMock = $this->getClientMock();
        $analyzer = new Analyzer(null, $clientMock);
        $results = $analyzer->analyzeUrl('http://www.example.org', 'keyword');
        $this->assertTrue(is_array($results));
        $this->assertEquals(count($analyzer->getMetrics()), count($results));
        $this->assertArrayHasKey('analysis', current($results));
        $this->assertArrayHasKey('name', current($results));
        $this->assertArrayHasKey('description', current($results));
        $this->assertArrayHasKey('value', current($results));
        $this->assertArrayHasKey('negative_impact', current($results));
    }

    /**
     * @expectedException \SeoAnalyzer\HttpClient\Exception\HttpException
     *
     * @throws ReflectionException
     */
    public function testAnalyzeUrlFailOnInvalidUrl()
    {
        (new Analyzer())->analyzeUrl('invalid-url');
    }

    /**
     * @throws HttpException
     * @throws ReflectionException
     */
    public function testAnalyzeFilePass()
    {
        $clientMock = $this->getClientMock();
        $analyzer = new Analyzer(null, $clientMock);
        $results = $analyzer->analyzeFile(dirname(__DIR__) . '/data/test.html');
        $this->assertTrue(is_array($results));
        $this->assertEquals(count($analyzer->getMetrics()), count($results));
        $this->assertArrayHasKey('analysis', current($results));
        $this->assertArrayHasKey('name', current($results));
        $this->assertArrayHasKey('description', current($results));
        $this->assertArrayHasKey('value', current($results));
        $this->assertArrayHasKey('negative_impact', current($results));
    }

    /**
     * @throws ReflectionException
     */
    public function testAnalyzeHtmlPass()
    {
        $clientMock = $this->getClientMock();
        $analyzer = new Analyzer(null, $clientMock);
        $htmlString =  file_get_contents(dirname(__DIR__) . '/data/test.html');
        $results = $analyzer->analyzeHtml($htmlString);
        $this->assertTrue(is_array($results));
        $this->assertEquals(count($analyzer->getMetrics()), count($results));
        $this->assertArrayHasKey('analysis', current($results));
        $this->assertArrayHasKey('name', current($results));
        $this->assertArrayHasKey('description', current($results));
        $this->assertArrayHasKey('value', current($results));
        $this->assertArrayHasKey('negative_impact', current($results));
    }

    /**
     * @throws HttpException
     * @throws ReflectionException
     */
    public function testAnalyzePass()
    {
        $page = new Page();
        $page->content = file_get_contents(dirname(__DIR__) . '/data/test.html');
        $analyzer = new Analyzer($page);
        $results = $analyzer->analyze();
        $this->assertTrue(is_array($results));
        $this->assertEquals(count($analyzer->getMetrics()), count($results));
        $this->assertArrayHasKey('analysis', current($results));
        $this->assertArrayHasKey('name', current($results));
        $this->assertArrayHasKey('description', current($results));
        $this->assertArrayHasKey('value', current($results));
        $this->assertArrayHasKey('negative_impact', current($results));
    }

    /**
     * @expectedException \InvalidArgumentException No
     */
    public function testAnalyzeFailOnNoPage()
    {
        $analyzer = new Analyzer();
        $analyzer->analyze();
    }

    /**
     * @throws HttpException
     * @throws ReflectionException
     */
    public function testAnalyzePassInEnglishAsDefault()
    {
        $page = new Page();
        $page->content = '<html lang="en"></html>';
        $analyzer = new Analyzer($page);
        $results = $analyzer->analyze();
        $this->assertEquals('The size of your page is ok', $results['PageContentSize']['analysis']);
    }

    /**
     * @throws HttpException
     * @throws ReflectionException
     */
    public function testAnalyzePassInPolish()
    {
        $page = new Page();
        $page->content = '<html lang="en"></html>';
        $analyzer = new Analyzer($page);
        $analyzer->locale = 'pl_PL';
        $results = $analyzer->analyze();
        $this->assertEquals('Rozmiar strony jest w porządku', $results['PageContentSize']['analysis']);
    }

    /**
     * @throws HttpException
     * @throws ReflectionException
     */
    public function testAnalyzePassOnEmptyPageContent()
    {
        $page = new Page();
        $page->content = '<html lang="en"></html>';
        $analyzer = new Analyzer($page);
        $results = $analyzer->analyze();
        $this->assertTrue(is_array($results));
        $this->assertEquals(count($analyzer->getMetrics()), count($results));
        $this->assertArrayHasKey('analysis', current($results));
        $this->assertArrayHasKey('name', current($results));
        $this->assertArrayHasKey('description', current($results));
        $this->assertArrayHasKey('value', current($results));
        $this->assertArrayHasKey('negative_impact', current($results));
    }

    /**
     * @throws HttpException
     * @throws ReflectionException
     */
    public function testAnalyzePassOnInvalidHtml()
    {
        $page = new Page();
        $page->content = "<html lang='en'><body><dif>hrad>><r\"o<!dif? \'dfgdf'''';< html>";
        $analyzer = new Analyzer($page);
        $results = $analyzer->analyze();
        $this->assertTrue(is_array($results));
        $this->assertEquals(count($analyzer->getMetrics()), count($results));
        $this->assertArrayHasKey('analysis', current($results));
        $this->assertArrayHasKey('name', current($results));
        $this->assertArrayHasKey('description', current($results));
        $this->assertArrayHasKey('value', current($results));
        $this->assertArrayHasKey('negative_impact', current($results));
    }

    /**
     * @throws HttpException
     * @throws ReflectionException
     */
    public function testGetMetricsPass()
    {
        $clientMock = $this->getClientMock();
        $page = new Page();
        $page->content = '<html lang="en"></html>';
        $analyzer = new Analyzer($page, $clientMock);
        $metrics = $analyzer->getMetrics();
        $this->assertTrue(is_array($metrics));
        foreach ($metrics as $metric) {
            $this->assertInstanceOf(AbstractMetric::class, $metric);
        }
    }
}
