<?php

namespace SeoAnalyzer\Metric\Page\Content;

use SeoAnalyzer\Metric\AbstractMetric;

class RatioMetric extends AbstractMetric
{
    public $description = 'The ratio of page content to page code [%]';

    public function __construct($inputData)
    {
        $ratio = 0;
        if (!empty($inputData['code_size'])) {
            $ratio = round($inputData['content_size'] / $inputData['code_size'] * 100);
        }
        parent::__construct($ratio);
    }

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        if ($this->value < 10) {
            $this->impact = 8;
            return 'Content to code ratio is too low. Consider adding more text to your page or remove some unnecessary html code';
        }
        if ($this->value < 20) {
            $this->impact = 5;
            return 'Consider adding more text to your page or remove unnecessary html code. Good content have crucial impact on SEO';
        }
        return 'Page has good content to code ratio';
    }
}
