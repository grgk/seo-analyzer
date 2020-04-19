<?php

namespace SeoAnalyzer\Metric\Page\Content;

use SeoAnalyzer\Metric\AbstractMetric;

class SizeMetric extends AbstractMetric
{
    public $description = 'The size of the page';

    protected $results = [
        'read_error' => [
            self::IMPACT => 10,
            self::MESSAGE => 'Can not read your page content'
        ],
        'empty_content' => [
            self::IMPACT => 10,
            self::MESSAGE => 'Looks that your site content is empty'
        ],
        'very_big' => [
            self::IMPACT => 3,
            self::MESSAGE => 'The site is very big. You should consider rebuilding the page to optimise its size'
        ],
        'too_big' => [
            self::IMPACT => 1,
            self::MESSAGE => 'You should consider some optimisation of the page to decrease its size'
        ],
    ];

    public function __construct($inputData)
    {
        parent::__construct($inputData);
        $conditions = [
            'read_error' => $this->value === false,
            'empty_content' => $this->value === 0,
            'very_big' => $this->value > 80000,
            'too_big' => $this->value > 30000,
        ];
        $this->setUpResultsConditions($conditions);
    }

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        return $this->checkTheResults('The size of your page is ok');
    }
}
