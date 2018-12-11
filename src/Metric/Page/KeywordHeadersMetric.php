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
        if (empty($this->value['headers']['h1'][0])
            or stripos($this->value['headers']['h1'][0], $this->value['keyword']) === false) {
            $this->impact = 7;
            return 'The main H1 header does not contain the keyword phrase. Adding it could strongly improve SEO';
        }
        if (!empty($this->value['headers']['h2'])) {
            $anyH2HeaderIncludeKeyword = false;
            foreach ($this->value['headers']['h2'] as $h2) {
                if (stripos($h2, $this->value['keyword']) !== false) {
                    $anyH2HeaderIncludeKeyword = true;
                }
            }
            if ($anyH2HeaderIncludeKeyword) {
                return 'Good! The site headers contain the keyword phrase';
            }

        }
        $this->impact = 3;
        return 'The site H2 headers does not contain the keyword phrase. Adding it could strongly improve SEO';
    }
}
