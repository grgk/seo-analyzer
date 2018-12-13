<?php

namespace SeoAnalyzer\Metric\Page;

use SeoAnalyzer\Metric\AbstractMetric;

class SizeMetric extends AbstractMetric
{
    public $description = 'The size of the page';

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        if ($this->value === false) {
            $this->impact = 10;
            return "Can not read your page content";
        }
        if ($this->value === 0) {
            $this->impact = 10;
            return "Looks that your site content is empty";
        }
        if ($this->value > 80000) {
            $this->impact = 3;
            return "The site is very big. You should consider rebuilding the page to optimise it's size";
        }
        if ($this->value > 30000) {
            $this->impact = 1;
            return "You should consider some optimisation of the page to decrease it's size";
        }
        return 'The size of your page is ok';
    }
}
