<?php
namespace Tests\TestCase\Metric;

use ReflectionException;
use Tests\TestCase;
use Tests\TestCase\Metric\Mock\ConditionsCallableTestMetric;

class ConditionsCallableMetricTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testAnalyzePassWithMetricsCallableConditions()
    {
        $metric = new ConditionsCallableTestMetric('not empty value');
        $this->assertEquals('Success test metric output message', $metric->analyze());
        $this->assertEquals($metric->impact, 0);
    }

    /**
     * @throws ReflectionException
     */
    public function testAnalyzeFailWithMetricsCallableConditions()
    {
        $metric = new ConditionsCallableTestMetric(null);
        $this->assertEquals('Fail test metric output message', $metric->analyze());
        $this->assertEquals($metric->impact, 4);
    }
}
