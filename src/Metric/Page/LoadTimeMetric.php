<?php

namespace SeoAnalyzer\Metric\Page;

use SeoAnalyzer\Metric\AbstractMetric;

class LoadTimeMetric extends AbstractMetric
{
    public $description = 'Time used to load the page [sec.]';

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        $this->value = round($this->value, 2);
        if ($this->value > 3) {
            $this->impact = 8;
            return 'The site takes very long to load. You should definitely consider rebuilding the page and/or change the hosting provider';
        }
        if ($this->value > 1) {
            $this->impact = 2;
            return 'You should optimise your site for faster loading, as this could have strong impact on SEO';
        }
        return 'The site loads very fast';
    }
}
