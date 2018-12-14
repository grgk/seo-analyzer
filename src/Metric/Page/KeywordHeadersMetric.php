<?php

namespace SeoAnalyzer\Metric\Page;

use SeoAnalyzer\Metric\AbstractMetric;

class KeywordHeadersMetric extends AbstractMetric
{
    public $description = 'Does the headers contain a key phrase?';

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        if (!empty($this->value['headers']['h2'])) {
            $anyHasKeyword = false;
            foreach ($this->value['headers']['h2'] as $h2) {
                if (stripos($h2, $this->value['keyword']) !== false) {
                    $anyHasKeyword = true;
                }
            }
        }

        switch (true) {
            case (empty($this->value['headers']['h1'][0])
                || stripos($this->value['headers']['h1'][0], $this->value['keyword']) === false):
                $this->impact = 7;
                $message = 'The main H1 header does not contain the keyword phrase. Adding it could strongly improve SEO';
            break;
            case ($anyHasKeyword !== false):
                $message = 'Good! The site headers contain the keyword phrase';
                break;
            default:
                $this->impact = 3;
                $message = 'The site H2 headers does not contain the keyword phrase. Adding it could strongly improve SEO';
                break;
        }
        return $message;
    }
}
