<?php

namespace SeoAnalyzer\Metric\Page\Keyword;

use SeoAnalyzer\Metric\AbstractMetric;
use SeoAnalyzer\Metric\KeywordBasedMetricInterface;

class HeadersMetric extends AbstractMetric implements KeywordBasedMetricInterface
{
    public $description = 'Does the headers contain a key phrase?';

    protected $results = [
        'no_keyword_h1' => [
            self::IMPACT => 7,
            self::MESSAGE => 'The main H1 header does not contain the keyword phrase. Adding it could strongly improve SEO'
        ],
        'no_keyword_h2' => [
            self::IMPACT => 3,
            self::MESSAGE => 'The site H2 headers does not contain the keyword phrase. Adding it could strongly improve SEO'
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
    protected function setUpResultsConditions(array $conditions = []): bool
    {
        $conditions = [
            'no_keyword_h1' => $this->isKeywordMissingInHeaders(),
            'no_keyword_h2' => $this->isKeywordMissingInHeaders('h2')
        ];
        return parent::setUpResultsConditions($conditions);
    }

    /**
     * Checks if keyword is not present in headers of specified type.
     *
     * @param string $headerType
     * @return bool
     */
    private function isKeywordMissingInHeaders(string $headerType = 'h1'): bool
    {
        if (empty($this->value[self::HEADERS]) || empty($this->value[self::HEADERS][$headerType])) {
            return true;
        }
        $keywordNotFound = true;
        foreach ($this->value[self::HEADERS][$headerType] as $headerContent) {
            if (stripos($headerContent, $this->value['keyword']) !== false) {
                $keywordNotFound = false;
            }
        }
        return $keywordNotFound;
    }
}
