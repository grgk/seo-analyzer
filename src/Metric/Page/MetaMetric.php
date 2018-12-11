<?php

namespace SeoAnalyzer\Metric\Page;

use SeoAnalyzer\Metric\AbstractMetric;

class MetaMetric extends AbstractMetric
{
    public $description = 'Html meta tags information';

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        if (strlen($this->value['title']) < 10 or strlen($this->value['title']) > 60) {
            $this->impact = 5;
            return 'The page title length should be between 10 and 60 characters. Title should also include your main keyword';
        }
        if (empty($this->value['meta']['description'])) {
            $this->impact = 5;
            return 'Missing page meta description tag. We strongly recommend to add it. It should be between 30 and 120 characters and should include your main keyword';
        }
        if (strlen($this->value['meta']['description']) < 30 or strlen($this->value['meta']['description']) > 120) {
            $this->impact = 3;
            return 'The page meta description length should be between 30 and 120 characters. Description should also include your main keyword';
        }
        return 'The site meta tags look good';
    }
}
