<?php
namespace Tests\TestCase\Metric\Page;

use SeoAnalyzer\Metric\Page\HeadersMetric;
use Tests\TestCase;

class HeadersMetricTest extends TestCase
{
    /**
     * @dataProvider metricsDataProvider
     * @throws \ReflectionException
     */
    public function testAnalyzePass($value, array $expected)
    {
        $metric = new HeadersMetric($value);
        $message = $metric->analyze();
        $this->assertContains($expected['message'], $message);
        $this->assertEquals($metric->impact, $expected['impact']);
    }

    public function metricsDataProvider()
    {
        return [
            [[], ['message' => 'Looks the site has no headers at all. You should rebuild your page', 'impact' => 7]],
            [['h2' => ['Lorem ipsum']], ['message' => 'There is no H1 header on the site. You should', 'impact' => 5]],
            [['h1'], ['message' => 'There is no H1 header on the site. You should rebuild your page', 'impact' => 5]],
            [['h1' => ''], ['message' => 'There is no H1 header on the site. You should rebuild your page', 'impact' => 5]],
            [['h1' => []], ['message' => 'There is no H1 header on the site. You should rebuild your page', 'impact' => 5]],
            [['h1' => ['']], ['message' => 'There is no H1 header on the site. You should rebuild your page', 'impact' => 5]],
            [['h1' => ['Lorem', 'ipsum']], ['message' => 'There are multiple H1 headers on the site.', 'impact' => 3]],
            [
                ['h1' => ['Too long H1 header content Lorem ipsum']],
                ['message' => 'The H1 header is too long. You should consider changing it', 'impact' => 3]
            ],
            [['h1' => ['Lorem']], ['message' => 'There are no H2 headers on the site. You should', 'impact' => 3]],
            [['h1' => ['Lorem'], 'h2' => ''], ['message' => 'There are no H2 headers on the site.', 'impact' => 3]],
            [['h1' => ['Lorem'], 'h2' => []], ['message' => 'There are no H2 headers on the site.', 'impact' => 3]],
            [['h1' => ['Lorem'], 'h2' => ['']], ['message' => 'There are no H2 headers on the site.', 'impact' => 3]],
            [
                ['h1' => ['Lorem'], 'h2' => ['Lorem', 'ipsum', 'dolor', 'sit', 'lorem', 'ipusm']],
                ['message' => 'There are a lot of H2 headers on the site. You should limit', 'impact' => 1]
            ],
            [
                ['h1' => ['Lorem'], 'h2' => ['Lorem', 'ipsum', 'dolor']],
                ['message' => 'There are no H3 header on the site. Using proper headers structure can', 'impact' => 1]
            ],
            [
                ['h1' => ['Lorem'], 'h2' => ['Lorem', 'ipsum', 'dolor'], 'h3' => ''],
                ['message' => 'There are no H3 header on the site. Using proper headers structure can', 'impact' => 1]
            ],
            [
                ['h1' => ['Lorem'], 'h2' => ['Lorem', 'ipsum', 'dolor'], 'h3' => []],
                ['message' => 'There are no H3 header on the site. Using proper headers structure can', 'impact' => 1]
            ],
            [
                ['h1' => ['Lorem'], 'h2' => ['Lorem', 'ipsum', 'dolor'], 'h3' => ['']],
                ['message' => 'There are no H3 header on the site. Using proper headers structure can', 'impact' => 1]
            ],
                        [
                ['h1' => ['Lorem'], 'h2' => ['Lorem', 'ipsum', 'dolor'], 'h3' => ['lorem', 'ipsum']],
                ['message' => 'The headers structure on the site looks very good', 'impact' => 0]
            ]
        ];
    }
}
