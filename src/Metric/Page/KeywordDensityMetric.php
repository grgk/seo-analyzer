<?php

namespace SeoAnalyzer\Metric\Page;

class KeywordDensityMetric extends AbstractKeywordDensityMetric
{
    public $description = 'Keyword density in page content';

    /**
     * @inheritdoc
     */
    public function analyze(): string
    {
        $keywords = $this->analyseKeywords($this->value['text'], $this->value['stop_words']);
        unset($this->value);
        $this->value['keywords'] = $keywords;
        $overusedWords = $this->getOverusedKeywords($keywords);

        if (!empty($this->keyword)) {
            $this->name = 'KeywordDensityKeyword';
            unset($this->value);
            $this->value['keywords'] = $keywords;
            $this->value['keyword'] = $this->keyword;
            if (!empty($keywords)) {
                foreach ($keywords as $group) {
                    foreach ($group as $phrase => $count) {
                        if (stripos($phrase, $this->keyword) !== false) {
                            if (in_array($this->keyword, $overusedWords)) {
                                $this->impact = 4;
                                return 'The key phrase is overused on the site. Try to reduce its occurrence';
                            }
                            return 'Good! The key phrase is present in most popular keywords on the site';
                        }
                    }
                }
            }
            $this->impact = 4;
            return 'You should consider adding your keyword to the site content';
        }

        if (!empty($overusedWords)) {
            $this->value['overused'] = $overusedWords;
            $this->impact = 3;
            return 'There are some overused keywords on site. You should consider limiting the use of overused phrases';
        }
        return 'The keywords density on the site looks good';
    }
}
