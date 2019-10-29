<?php

namespace SeoAnalyzer\Metric\Page;

use SeoAnalyzer\Metric\AbstractMetric;

class HeadersMetric extends AbstractMetric
{
    public $description = 'Html headers metric';

    protected $results = [
        'no_headers' => [
            'impact' => 7,
            'message' => 'Looks the site has no headers at all.' .
                ' You should rebuild your page structure as html headers have strong impact on SEO'
        ],
        'no_H1' => [
            'impact' => 5,
            'message' => 'There is no H1 header on the site.' .
                ' You should rebuild your page to use main headers as this has strong impact on SEO'
        ],
        'multi_H1' => [
            'impact' => 3,
            'message' => 'There are multiple H1 headers on the site.' .
                ' You should use only one main header on the site'
        ],
        'too_long_H1' => [
            'impact' => 3,
            'message' => 'The H1 header is too long.' .
                ' You should consider changing it to something shorter including your main keyword',
        ],
        'no_H2' => [
            'impact' => 3,
            'message' => 'There are no H2 headers on the site.' .
                ' You should consider rebuild your page to use proper headers structure'
        ],
        'too_many_H2' => [
            'impact' => 1,
            'message' => 'There are a lot of H2 headers on the site. You should limit number of H2 headers'
        ],
        'no_H3' => [
            'impact' => 1,
            'message' => 'There are no H3 header on the site. Using proper headers structure can improve the SEO'
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
        return $this->checkTheResults('The headers structure on the site looks very good');
    }

    /**
     * Sets up the metric conditions for the configured results.
     */
    protected function setUpResultsConditions()
    {
        $this->results['no_headers']['condition'] = empty($this->value);
        if (!empty($this->value)) {
            $this->results['no_H1']['condition'] = empty($this->value['h1']) || empty($this->value['h1'][0]);
            if (!empty($this->value['h1'])) {
                $this->results['multi_H1']['condition'] = count($this->value['h1']) > 1;
                $this->results['too_long_H1']['condition'] = strlen($this->value['h1'][0]) > 35;
            }
            $this->results['no_H2']['condition'] = empty($this->value['h2']) || empty($this->value['h2'][0]);
            $this->results['too_many_H2']['condition'] = !empty($this->value['h2']) && count($this->value['h2']) > 5;
            $this->results['no_H3']['condition'] = empty($this->value['h3']) || empty($this->value['h3'][0]);
        }
    }
}
