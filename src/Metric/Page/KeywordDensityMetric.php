<?php

namespace SeoAnalyzer\Metric\Page;

use SeoAnalyzer\Factor;
use SeoAnalyzer\Metric\KeywordBasedMetricInterface;

class KeywordDensityMetric extends AbstractKeywordDensityMetric implements KeywordBasedMetricInterface
{
    public $description = 'Keyword density in page content';

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        $keywords = $this->analyseKeywords($this->value['text'], $this->value['stop_words']);
        unset($this->value);
        $this->value[Factor::KEYWORDS] = $keywords;
        $overusedWords = $this->getOverusedKeywords($keywords);
        if (!empty($this->keyword)) {
            return $this->analyzeWithKeyword($keywords, $overusedWords);
        }
        if (!empty($overusedWords)) {
            $this->value['overused'] = $overusedWords;
            $this->impact = 3;
            return 'There are some overused keywords on site. You should consider limiting the use of overused phrases';
        }
        return 'The keywords density on the site looks good';
    }

    protected function analyzeWithKeyword(array $keywords, array $overusedWords): string
    {
        $this->name = 'KeywordDensityKeyword';
        unset($this->value);
        $this->value[Factor::KEYWORDS] = $keywords;
        $this->value[Factor::KEYWORD] = $this->keyword;
        $isPresent = false;
        foreach ($this->getPhrases() as $phrase) {
            if (stripos($phrase, $this->keyword) !== false) {
                if (in_array($this->keyword, $overusedWords)) {
                    $this->impact = 4;
                    return 'The key phrase is overused on the site. Try to reduce its occurrence';
                }
                $isPresent = true;
            }
        }
        if (!$isPresent) {
            $this->impact = 4;
            return 'You should consider adding your keyword to the site content';
        }
        return 'Good! The key phrase is present in most popular keywords on the site';
    }

    private function getPhrases()
    {
        return array_keys(array_merge(...$this->value[Factor::KEYWORDS]));
    }
}
