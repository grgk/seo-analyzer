<?php

namespace SeoAnalyzer\Metric\Page;

use SeoAnalyzer\Metric\AbstractMetric;

class MetaMetric extends AbstractMetric
{
    public $description = 'Html meta tags information';

    protected $results = [
        'no_tags' => [
            'impact' => 8,
            'message' => 'Missing page title and description meta tags. You should add the title meta tag at least'
        ],
        'title_length' => [
            'impact' => 5,
            'message' => 'The page title length should be between 10 and 60 characters.' .
                ' Title should also include your main keyword'
        ],
        'missing_description' => [
            'impact' => 5,
            'message' => 'Missing page meta description tag. We strongly recommend to add it.' .
                ' It should be between 30 and 120 characters and should include your main keyword'
        ],
        'description_length' => [
            'impact' => 3,
            'message' => 'The page meta description length should be between 30 and 120 characters.' .
                ' Description should also include your main keyword'
        ]
    ];

    public function __construct($inputData)
    {
        parent::__construct($inputData);
        $this->setUpResultsConditions();
    }

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        return $this->checkTheResults('The site meta tags look good');
    }

    /**
     * Sets up the metric conditions for the configured results.
     */
    protected function setUpResultsConditions()
    {
        $this->results['no_tags']['condition'] = empty($this->value);
        if (!empty($this->value)) {
            $this->results['title_length']['condition'] = isset($this->value['title'])
                && (strlen($this->value['title']) < 10 || strlen($this->value['title']) > 60);
            $this->results['missing_description']['condition'] = isset($this->value['meta'])
                && empty($this->value['meta'][self::DESCRIPTION]);
            $this->results['description_length']['condition'] = isset($this->value['meta'][self::DESCRIPTION])
                && (strlen($this->value['meta'][self::DESCRIPTION]) < 30
                || strlen($this->value['meta'][self::DESCRIPTION]) > 120);
        }
    }
}
