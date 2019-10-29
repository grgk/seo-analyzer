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
     * @inheritDoc
     */
    protected function setUpResultsConditions(array $conditions = []): bool
    {
        $conditions = ['no_tags' => empty($this->value)];
        if (empty($this->value)) {
            return parent::setUpResultsConditions($conditions);
        }
        $conditions = array_merge($conditions, [
            'title_length' => $this->checkTitleTag(),
            'missing_description' => isset($this->value['meta']) && empty($this->value['meta'][self::DESCRIPTION]),
            'description_length' => $this->checkMetaDescriptionTag()
        ]);
        return parent::setUpResultsConditions($conditions);
    }

    private function checkTitleTag($minLength = 10, $maxLength = 60)
    {
        return isset($this->value['title'])
            && (strlen($this->value['title']) < $minLength || strlen($this->value['title']) > $maxLength);
    }

    private function checkMetaDescriptionTag($minLength = 30, $maxLength = 120)
    {
        return isset($this->value['meta'][self::DESCRIPTION])
            && (strlen($this->value['meta'][self::DESCRIPTION]) < $minLength
                || strlen($this->value['meta'][self::DESCRIPTION]) > $maxLength);
    }
}
