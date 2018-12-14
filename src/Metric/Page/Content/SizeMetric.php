<?php

namespace SeoAnalyzer\Metric\Page\Content;

use SeoAnalyzer\Metric\AbstractMetric;

class SizeMetric extends AbstractMetric
{
    public $description = 'The size of the page';

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        switch (true) {
            case ($this->value === false):
                $this->impact = 10;
                $message = "Can not read your page content";
                break;
            case ($this->value === 0):
                $this->impact = 10;
                $message = "Looks that your site content is empty";
                break;
            case ($this->value > 80000):
                $this->impact = 3;
                $message = "The site is very big. You should consider rebuilding the page to optimise it's size";
                break;
            case ($this->value > 30000):
                $this->impact = 1;
                $message = "You should consider some optimisation of the page to decrease it's size";
                break;
            default:
                $message = 'The size of your page is ok';
                break;
        }
        return $message;
    }
}
