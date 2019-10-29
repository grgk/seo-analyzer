<?php

namespace SeoAnalyzer\Metric\Page\Content;

use SeoAnalyzer\Metric\AbstractMetric;

class SizeMetric extends AbstractMetric
{
    public $description = 'The size of the page';

    public function __construct($inputData)
    {
        parent::__construct($inputData);
        $this->results = [
            'read_error' => [
                'condition' => $this->value === false,
                'impact' => 10,
                'message' => 'Can not read your page content'
            ],
            'empty_content' => [
                'condition' => $this->value === 0,
                'impact' => 10,
                'message' => 'Looks that your site content is empty'
            ],
            'very_big' => [
                'condition' => $this->value > 80000,
                'impact' => 3,
                'message' => 'The site is very big. You should consider rebuilding the page to optimise it\'s size'
            ],
            'too_big' => [
                'condition' => $this->value > 30000,
                'impact' => 1,
                'message' => 'You should consider some optimisation of the page to decrease it\'s size'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        return $this->checkTheResults('The size of your page is ok');
    }
}
