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
        switch (true) {
            case (empty($this->value)):
                $this->impact = 7;
                $message = 'Looks the site has no headers at all. You should rebuild your page structure as html headers have strong impact on SEO';
                break;
            case (empty($this->value['h1'])):
                $this->impact = 5;
                $message = 'There is no H1 header on the site. You should rebuild your page to use main headers as this has strong impact on SEO';
                break;
            case (count($this->value['h1']) > 1):
                $this->impact = 3;
                $message = 'There are multiple H1 headers on the site. You should use only one main header on the site';
                break;
            case (strlen($this->value['h1'][0]) > 35):
                $this->impact = 3;
                $message = 'The H1 header is too long. You should consider changing it to something shorter including your main keyword';
                break;
            case (empty($this->value['h2'])):
                $this->impact = 3;
                $message = 'There are no H2 headers on the site. You should consider rebuild your page to use proper headers structure';
                break;
            case (count($this->value['h2']) > 5):
                $this->impact = 1;
                $message = 'There are a lot of H2 headers on the site. You should limit number of H2 headers';
                break;
            case (empty($this->value['h3'])):
                $this->impact = 1;
                $message = 'There are no H3 header on the site. Using proper headers structure can improve the SEO';
                break;
            default:
                $message = 'The headers structure on the site looks very good';
                break;
        }
        return $message;
    }
}
