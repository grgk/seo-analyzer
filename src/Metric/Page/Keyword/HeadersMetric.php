<?php

namespace SeoAnalyzer\Metric\Page\Keyword;

use SeoAnalyzer\Metric\AbstractMetric;

class HeadersMetric extends AbstractMetric
{
    public $description = 'Does the headers contain a key phrase?';

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        if (empty($this->value[self::HEADERS]['h1'][0])
            || stripos($this->value[self::HEADERS]['h1'][0], $this->value['keyword']) === false) {
            $this->impact = 7;
            return 'The main H1 header does not contain the keyword phrase. Adding it could strongly improve SEO';
        }
        if (!empty($this->value[self::HEADERS]['h2'])) {
            $anyHasKeyword = false;
            foreach ($this->value[self::HEADERS]['h2'] as $h2) {
                if (stripos($h2, $this->value['keyword']) !== false) {
                    $anyHasKeyword = true;
                }
            }
            if ($anyHasKeyword) {
                return 'Good! The site headers contain the keyword phrase';
            }
        }
        $this->impact = 3;
        return 'The site H2 headers does not contain the keyword phrase. Adding it could strongly improve SEO';
    }
}
