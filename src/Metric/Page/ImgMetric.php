<?php

namespace SeoAnalyzer\Metric\Page;

use SeoAnalyzer\Metric\AbstractMetric;

class ImgMetric extends AbstractMetric
{
    public $description = 'Image weight';
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
            default:
                $message = 'Good! You have images on your page';
                break;
        }
        return $message;
    }
}
