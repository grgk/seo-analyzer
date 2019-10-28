<?php

namespace SeoAnalyzer\Metric\Page;

use SeoAnalyzer\Metric\AbstractMetric;

class HeadersMetric extends AbstractMetric
{
    public $description = 'Html headers metric';

    protected $results = [];

    public function __construct($inputData)
    {
        parent::__construct($inputData);
        $this->results = [
            'no_headers' => [
                'condition' => empty($this->value),
                'impact' => 7,
                'message' => 'Looks the site has no headers at all. You should rebuild your page structure as html headers have strong impact on SEO'
            ],
            'no_H1' => [
                'condition' => empty($this->value['h1']),
                'impact' => 5,
                'message' => 'There is no H1 header on the site. You should rebuild your page to use main headers as this has strong impact on SEO'
            ],
            'multi_H1' => [
                'condition' => count($this->value['h1']) > 1,
                'impact' => 3,
                'message' => 'There are multiple H1 headers on the site. You should use only one main header on the site'
            ],
            'too_long_H1' => [
                'condition' => strlen($this->value['h1'][0]) > 35,
                'impact' => 3,
                'message' => 'The H1 header is too long. You should consider changing it to something shorter including your main keyword',
            ],
            'no_H2' => [
                'condition' => empty($this->value['h2']),
                'impact' => 3,
                'message' => 'There are no H2 headers on the site. You should consider rebuild your page to use proper headers structure'
            ],
            'too_many_H2' => [
                'condition' => count($this->value['h2']) > 5,
                'impact' => 1,
                'message' => 'There are a lot of H2 headers on the site. You should limit number of H2 headers'
            ],
            'no_H3' => [
                'condition' => empty($this->value['h3']),
                'impact' => 1,
                'message' => 'There are no H3 header on the site. Using proper headers structure can improve the SEO'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        foreach ($this->results as $result) {
            if ($result['condition']) {
                $this->impact = $result['impact'];
                return $result['message'];
            }
        }
        return 'The headers structure on the site looks very good';
    }
}
