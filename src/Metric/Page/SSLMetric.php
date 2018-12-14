<?php

namespace SeoAnalyzer\Metric\Page;

use SeoAnalyzer\Metric\AbstractMetric;

class SSLMetric extends AbstractMetric
{
    public $description = 'Does the site use an encrypted connection?';

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        if (empty($this->value)) {
            $this->impact = 3;
            return 'You should use encrypted connection, as this could have strong impact on SEO';
        }
        return 'yes';
    }
}
