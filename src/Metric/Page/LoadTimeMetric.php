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
        if ($this->value === false) {
            return 'The page load time could not be measured';
        }
        $this->value = round($this->value, 2);
        switch (true) {
            case ($this->value > 3):
                $this->impact = 8;
                $message = 'The site takes very long to load. You should definitely consider rebuilding the page and/or change the hosting provider';
                break;
            case ($this->value > 1):
                $this->impact = 2;
                $message = 'You should optimise your site for faster loading, as this could have strong impact on SEO';
                break;
            default:
                $message = 'The site loads very fast';
                break;
        }
        return $message;
    }
}
