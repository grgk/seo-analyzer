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
        if (!empty($overusedWords = $this->getHeadersOverusedWords())) {
            $this->impact = 4;
            $this->value['overused'] = $overusedWords;
            return 'There are some overused keywords in headers. You should consider limiting the use of those phrases';
        }
        return 'The keywords density in headers looks good';
    }

    /**
     * Get overused words from headers.
     *
     * @return array
     */
    protected function getHeadersOverusedWords()
    {
        $keywords = $overusedWords = [];
        if (!empty($this->value['headers'])) {
            foreach ($this->value['headers'] as $header => $headersContent) {
                $keywords[$header] = $this->analyseKeywords(
                    implode(" ", $headersContent),
                    $this->value['stop_words'],
                    3
                );
                if (!empty($overUsed = $this->getOverusedKeywords($keywords[$header], 35, 3))) {
                    $overusedWords = array_merge($overusedWords, $overUsed);
                }
            }
            $this->value = $keywords;
        }
        return $overusedWords;
    }
}
