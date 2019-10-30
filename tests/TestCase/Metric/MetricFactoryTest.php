<?php
namespace Tests\TestCase\Metric;

use ReflectionException;
use SeoAnalyzer\Metric\MetricFactory;
use SeoAnalyzer\Metric\Page\Content\SizeMetric;
use Tests\TestCase;
use Tests\TestCase\Metric\Mock\MissingNameTestMetric;

class MetricFactoryTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testGetPass()
    {
        $metric = MetricFactory::get('page.content.size', 4076);
        $this->assertInstanceOf(SizeMetric::class, $metric);
        $this->assertEquals('The size of the page', $metric->description);
        $this->assertEquals(4076, $metric->value);
    }

    /**
     * @expectedException ReflectionException
     */
    public function testGetFailOnNotExistingClass()
    {
        MetricFactory::get('page.not_existing', 4076);
    }
}
