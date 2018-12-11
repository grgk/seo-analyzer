<?php

namespace SeoAnalyzer\Metric\Page;

use SeoAnalyzer\Metric\AbstractMetric;

class HeadersMetric extends AbstractMetric
{
    public $description = 'Html headers metric';

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        if (empty($this->value)) {
            $this->impact = 7;
            return 'Looks the site has no headers at all. You should rebuild your page structure as html headers have strong impact on SEO';
        }
        if (empty($this->value['h1'])) {
            $this->impact = 5;
            return 'There is no H1 header on the site. You should rebuild your page to use main headers as this has strong impact on SEO';
        }
        if (count($this->value['h1']) > 1) {
            $this->impact = 3;
            return 'There are multiple H1 headers on the site. You should use only one main header on the site';
        }
        if (strlen($this->value['h1'][0]) > 35) {
            $this->impact = 3;
            return 'The H1 header is too long. You should consider changing it to something shorter including your main keyword';
        }
        if (empty($this->value['h2'])) {
            $this->impact = 3;
            return 'There are no H2 headers on the site. You should consider rebuild your page to use proper headers structure';
        }
        if (count($this->value['h2']) > 5) {
            $this->impact = 1;
            return 'There are a lot of H2 headers on the site. You should limit number of H2 headers';
        }
        if (empty($this->value['h3'])) {
            $this->impact = 1;
            return 'There are no H3 header on the site. Using proper headers structure can improve the SEO';
        }
        return 'The headers structure on the site looks very good';
    }
}
