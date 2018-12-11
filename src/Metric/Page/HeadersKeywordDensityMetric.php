<?php

namespace SeoAnalyzer\Metric\Page;

class HeadersKeywordDensityMetric extends AbstractKeywordDensityMetric
{
    public $description = 'Keyword density in page headers';

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        $keywords = $overusedWords = $headers = [];
        if (!empty($this->value['headers'])) {
            foreach ($this->value['headers'] as $header => $headersContent) {
                $keywords[$header] = $this->analyseKeywords(
                    implode(" ", $headersContent),
                    $this->value['stop_words'],
                    3
                );
                $overUsed = $this->getOverusedKeywords($keywords[$header], 35, 3);
                if (!empty($overUsed)) {
                    $headers[] = $header;
                    $overusedWords = array_merge($overusedWords, $overUsed);
                }
            }
        }
        $this->value = $keywords;
        if (!empty($overusedWords)) {
            $this->impact = 4;
            $this->value['overused'] = $overusedWords;
            return 'There are some overused keywords in headers. You should consider limiting the use of those phrases';
        }
        return 'The keywords density in headers looks good';
    }
}
