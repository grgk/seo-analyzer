<?php
namespace Tests\TestCase\Metric\Page\Content;

use ReflectionException;
use SeoAnalyzer\Metric\Page\Content\SizeMetric;
use Tests\TestCase;

class SizeMetricTest extends TestCase
{
    /**
     * @param $value
     * @param array $expected
     * @throws ReflectionException
     * @dataProvider metricsDataProvider
     */
    public function testAnalyzePass($value, array $expected)
    {
        $metric = new SizeMetric($value);
        $message = $metric->analyze();
        $this->assertContains($expected['message'], $message);
        $this->assertEquals($metric->impact, $expected['impact']);
    }

    public function metricsDataProvider()
    {
        return [
            [false, ['message' => 'Can not read your page content', 'impact' => 10]],
            [0, ['message' => 'Looks that your site content is empty', 'impact' => 10]],
            [1000, ['message' => 'The size of your page is ok', 'impact' => 0]],
            [30000, ['message' => 'The size of your page is ok', 'impact' => 0]],
            [30001, ['message' => 'You should consider some optimisation of the page to decrease it', 'impact' => 1]],
            [80000, ['message' => 'You should consider some optimisation of the page to decrease it', 'impact' => 1]],
            [80001, ['message' => 'The site is very big. You should consider rebuilding the page to', 'impact' => 3]],
        ];
    }
}
