<?php
namespace Tests\TestCase\Metric;

use ReflectionException;
use SeoAnalyzer\Analyzer;
use SeoAnalyzer\Metric\MetricFactory;
use Symfony\Component\Translation\Translator;
use Tests\TestCase;

class MetricsTest extends TestCase
{
    /**
     * @var Translator
     */
    protected $translator;

    public function setUp()
    {
        parent::setUp();
        $analyzer = new Analyzer();
        $analyzer->setUpTranslator('pl_PL');
        $this->translator = $analyzer->translator;
    }

    /**
     * @param string $metricKey
     * @param mixed $input
     * @param array $expected
     * @dataProvider metricsDataProvider
     * @throws ReflectionException
     */
    public function testAnalyzePass(string $metricKey, $input, array $expected)
    {
        $metric = MetricFactory::get($metricKey, $input);
        $this->assertInstanceOf($expected['class'], $metric);
        $analysis = $metric->analyze();
        if (isset($expected['value'])) {
            $this->assertSame($expected['value'], $metric->value);
        }
        $this->assertEquals($expected['impact'], $metric->impact);
        $this->assertContains($expected['analysis'], $analysis);
        $this->assertNotEquals($metric->description, $this->translator->trans($metric->description));
        $this->assertNotEquals($analysis, $this->translator->trans($analysis));
    }

    public function metricsDataProvider()
    {
        return array_merge(
            require_once 'metricsTestData/file.php',
            require_once 'metricsTestData/keywords.php',
            require_once 'metricsTestData/page.php',
            require_once 'metricsTestData/pageHeaders.php',
            require_once 'metricsTestData/pageMeta.php'
        );
    }
}
