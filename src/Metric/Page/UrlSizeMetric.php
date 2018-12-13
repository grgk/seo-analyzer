<?php

namespace SeoAnalyzer\Metric\Page;

use SeoAnalyzer\Metric\AbstractMetric;

class UrlSizeMetric extends AbstractMetric
{
    public $description = 'The size of the page URL';

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        if ($this->value > 40) {
            $this->impact = 4;
            return "The site URL is very long. You should consider using some shorter URL";
        }
        if ($this->value > 20) {
            $this->impact = 1;
            return "You should consider using some shorter URL";
        }
        return 'The size of URL is ok';
    }
}
