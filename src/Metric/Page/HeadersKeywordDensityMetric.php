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
    protected function getHeadersOverusedWords(): array
    {
        $this->value = $this->getHeadersKeywords();
        $overusedWords = [];
        if (!empty($this->value)) {
            foreach ($this->value as $keywords) {
                $overusedWords = array_merge($overusedWords, $this->getOverusedKeywords($keywords, 35, 3));
            }
        }
        return $overusedWords;
    }

    /**
     * Returns headers keywords.
     *
     * @param int $maxPhaseWords
     * @return array
     */
    protected function getHeadersKeywords(int $maxPhaseWords = 3): array
    {
        $keywords = [];
        if (!empty($this->value['headers'])) {
            foreach ($this->value['headers'] as $header => $headersContent) {
                $keywords[$header] = $this->analyseKeywords(
                    implode(" ", $headersContent),
                    $this->value['stop_words'],
                    $maxPhaseWords
                );
            }
        }
        return $keywords;
    }
}
