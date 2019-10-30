<?php

namespace Tests\TestCase\Metric\Mock;

use SeoAnalyzer\Metric\AbstractMetric;

class ConditionsCallableTestMetric extends AbstractMetric
{
    public $description = 'Test metric';

    public function __construct($inputData)
    {
        parent::__construct($inputData);
        $this->results = [
            'test_condition' => [
                'condition' => function ($value) {
                    if (empty($value)) {
                        return true;
                    }
                    return false;
                },
                'impact' => 4,
                'message' => 'Fail test metric output message'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        return $this->checkTheResults('Success test metric output message');
    }
}
