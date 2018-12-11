<?php

namespace SeoAnalyzer\Metric\File;

use SeoAnalyzer\Metric\AbstractMetric;

class RobotsMetric extends AbstractMetric
{
    public $description = 'Does the site use proper robots.txt file?';

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        if (empty($this->value)) {
            $this->impact = 1;
            return 'no';
        } elseif (stripos($this->value, 'Disallow: /') !== false) {
            $this->impact = 5;
            return 'Robots.txt file blocks some parts of your site from indexing by search engines. Blocking content can have a big negative impact on SEO';
        }
        return 'yes';
    }
}
