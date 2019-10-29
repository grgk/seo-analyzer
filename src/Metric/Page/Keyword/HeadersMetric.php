<?php

namespace SeoAnalyzer\Metric\Page\Keyword;

use SeoAnalyzer\Metric\AbstractMetric;

class HeadersMetric extends AbstractMetric
{
    public $description = 'Does the headers contain a key phrase?';

    protected $results = [
        'no_keyword_h1' => [
            'impact' => 7,
            'message' => 'The main H1 header does not contain the keyword phrase. Adding it could strongly improve SEO'
        ],
        'no_keyword_h2s' => [
            'impact' => 3,
            'message' => 'The site H2 headers does not contain the keyword phrase. Adding it could strongly improve SEO'
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
        return $this->checkTheResults('Good! The site headers contain the keyword phrase');
    }

    /**
     * @inheritDoc
     */
    protected function setUpResultsConditions(array $conditions = [])
    {
        $conditions = [
            'no_keyword_h1' => empty($this->value[self::HEADERS]['h1'][0])
                || stripos($this->value[self::HEADERS]['h1'][0], $this->value['keyword']) === false,
            'no_keyword_h2s' => function ($value) {
                $keywordNotFound = true;
                if (!empty($value[self::HEADERS]['h2'])) {
                    foreach ($value[self::HEADERS]['h2'] as $h2) {
                        if (stripos($h2, $value['keyword']) !== false) {
                            $keywordNotFound = false;
                        }
                    }
                }
                return $keywordNotFound;
            }
        ];
        parent::setUpResultsConditions($conditions);
    }
}
