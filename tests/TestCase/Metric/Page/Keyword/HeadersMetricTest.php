<?php
namespace Tests\TestCase\Metric\Page\Keyword;

use SeoAnalyzer\Metric\Page\Keyword\HeadersMetric;
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
//            [[], ['message' => 'The main H1 header does not contain the keyword phrase.', 'impact' => 7]],
//            [
//                ['h1' => ['Lorem simsup dolor sit'], 'keyword' => 'ipsum'],
//                ['message' => 'The main H1 header does not contain the keyword phrase.', 'impact' => 7]
//            ],
//            [
//                ['headers' => ['h1' => ['Lorem ipsum dolor sit']], 'keyword' => 'ipsum'],
//                ['message' => 'The site H2 headers does not contain the keyword phrase. Adding it', 'impact' => 3]
//            ],
//            [
//                [
//                    'headers' => [
//                        'h1' => ['Lorem ipsum dolor sit'],
//                        'h2' => ['some lorem', 'dolor sit']
//                    ],
//                    'keyword' => 'ipsum'
//                ],
//                ['message' => 'The site H2 headers does not contain the keyword phrase. Adding it', 'impact' => 3]
//            ],
            [
                [
                    'headers' => [
                        'h1' => ['Lorem ipsum dolor sit'],
                        'h2' => ['First header', 'Some other lorem ipsum dolor sit']
                    ],
                    'keyword' => 'ipsum'
                ],
                ['message' => 'Good! The site headers contain the keyword phrase', 'impact' => 0]
            ],
        ];
    }
}
