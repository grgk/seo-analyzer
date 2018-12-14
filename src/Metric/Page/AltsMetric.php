<?php

namespace SeoAnalyzer\Metric\Page;

use SeoAnalyzer\Metric\AbstractMetric;

class AltsMetric extends AbstractMetric
{
    public $description = 'Alternate texts for images';

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        if (empty($this->value)) {
            return 'There is nothing to do here as there is no images on the site.';
        }
        $imagesCount = count($this->value);
        $altsCount = count(array_filter($this->value));
        $emptyAlts = $imagesCount - $altsCount;
        switch (true) {
            case ($emptyAlts > 10):
                $this->impact = 5;
                $message = 'There is a lot of images without alternate texts on your site. Every image should be described with alt attribute';
                break;
            case ($emptyAlts > 0):
                $this->impact = 3;
                $message = 'You should optimise your site adding missing alt descriptions to images, as this could have strong impact on SEO';
                break;
            default:
                $message = 'Good! All images on site have alternate descriptions';
                break;
        }
        return $message;
    }
}
