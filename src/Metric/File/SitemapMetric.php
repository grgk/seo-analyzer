<?php

namespace SeoAnalyzer\Metric\File;

use SeoAnalyzer\Metric\AbstractMetric;

class SitemapMetric extends AbstractMetric
{
    public $description = 'Does the site use a site map file "sitemap.xml"?';

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        if (empty($this->value)) {
            $this->impact = 1;
            return 'You should consider adding a sitemap.xml file, as this could help with indexing';
        }
        return 'yes';
    }
}
