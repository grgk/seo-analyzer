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
        switch (true) {
            case (strlen($this->value['title']) < 10 || strlen($this->value['title']) > 60):
                $this->impact = 5;
                $message = 'The page title length should be between 10 and 60 characters. Title should also include your main keyword';
                break;
            case (empty($this->value['meta'][self::DESCRIPTION])):
                $this->impact = 5;
                $message = 'Missing page meta description tag. We strongly recommend to add it. It should be between 30 and 120 characters and should include your main keyword';
                break;
            case (strlen($this->value['meta'][self::DESCRIPTION]) < 30 || strlen($this->value['meta'][self::DESCRIPTION]) > 120):
                $this->impact = 3;
                $message = "The page meta description length should be between 30 and 120 characters. Description should also include your main keyword";
                break;
            default:
                $message = 'The site meta tags look good';
                break;
        }
        return $message;
    }
}
