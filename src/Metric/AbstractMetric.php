<?php

namespace SeoAnalyzer\Metric;

use ReflectionClass;
use ReflectionException;

abstract class AbstractMetric implements MetricInterface
{
    const HEADERS = 'headers';
    const DESCRIPTION = 'description';
    const IMPACT = 'impact';
    const MESSAGE = 'message';

    /**
     * @var array Possible results configuration.
     */
    protected $results = [];

    /**
     * @var string Metric name
     */
    public $name;

    /**
     * @var string Metric description
     */
    public $description;

    /**
     * @var mixed Metric value
     */
    public $value;

    /**
     * @var int Negative impact on SEO. Higher value then bigger negative impact.
     */
    public $impact = 0;

    /**
     * @param mixed $inputData Input data to compute metric value
     * @throws ReflectionException
     */
    public function __construct($inputData)
    {
        if (empty($this->name)) {
            $this->name = str_replace(['SeoAnalyzer\\', 'Metric', '\\'], '', (new ReflectionClass($this))->getName());
        }
        $this->value = $inputData;
    }

    /**
     * Sets up the conditions for results configured.
     *
     * @param array $conditions
     * @return bool
     */
    protected function setUpResultsConditions(array $conditions): bool
    {
        foreach ($conditions as $key => $condition) {
            $this->results[$key]['condition'] = $condition;
        }
        return true;
    }

    /**
     * Checks if any of the possible defined results occurred.
     *
     * @param string $defaultMessage Default message to return
     * @return string Result message
     */
    protected function checkTheResults(string $defaultMessage): string
    {
        foreach ($this->results as $result) {
            if ($this->isResultExpected($result['condition'])) {
                $this->impact = $result['impact'];
                return $result['message'];
            }
        }
        return $defaultMessage;
    }

    private function isResultExpected($condition)
    {
        if (is_callable($condition)) {
            return $condition($this->value);
        } else {
            return $condition;
        }
    }
}
