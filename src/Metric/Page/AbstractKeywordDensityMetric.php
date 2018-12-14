<?php

namespace SeoAnalyzer\Metric\Page;

use SeoAnalyzer\Metric\AbstractMetric;

abstract class AbstractKeywordDensityMetric extends AbstractMetric
{
    public $description = 'Keyword density';

    public $keyword;

    public function __construct($inputData)
    {
        if (empty($inputData['stop_words'])) {
            $stopWordsFilename = dirname(__DIR__, 3) . '/locale/' . $inputData['locale'] . '_stop_words.yml';
            if (is_file($stopWordsFilename)) {
                $inputData['stop_words'] = file($stopWordsFilename);
            }
        }
        if (!empty($inputData['keyword'])) {
            $this->keyword = $inputData['keyword'];
        }
        parent::__construct($inputData);
    }

    /**
     * Cleans the text input and returns array of words.
     *
     * @param string $text
     * @param array $stopWords
     * @return array
     */
    protected function getWords(string $text, array $stopWords = []): array
    {
        $text = html_entity_decode($text);
        $stopWords = array_map(function ($word) {
            return trim($word);
        }, $stopWords);
        $stopWords = array_merge($stopWords, ['\'', '"', "-", "_"]);
        $text = strtolower(preg_replace('/[^a-zA-Z0-9\s]/', '', $text));
        $words = str_word_count($text, 1);
        $words = array_diff($words, $stopWords);
        return array_values(array_filter($words, function ($word) {
            return strlen($word) > 2;
        }));
    }

    /**
     * Returns most popular keywords used in text with it's use percentage.
     *
     * @param string $text
     * @param array $stopWords
     * @param int $maxPhraseWords
     * @param int $minCount Minimum keyword count
     * @return array
     */
    protected function analyseKeywords(string $text, array $stopWords, $maxPhraseWords = 4, $minCount = 0): array
    {
        $words = $this->getWords($text, $stopWords);
        $keywords = $this->getKeywords($words, $maxPhraseWords);
        for ($i = 1; $i <= $maxPhraseWords; $i++) {
            if (!empty($keywords[$i])) {
                $keywords[$i] = array_count_values($keywords[$i]);
                arsort($keywords[$i]);
                $keywords[$i] = array_filter($keywords[$i], function ($count) use ($minCount) {
                    return $count >= $minCount;
                });
                $keywordsCount = array_sum($keywords[$i]);
                foreach ($keywords[$i] as $keyword => $count) {
                    $keywords[$i][$keyword] = round($count / $keywordsCount * 100);
                }
                $keywords[$i] = array_slice($keywords[$i], 0, 10);
            }
        }
        return $keywords;
    }

    /**
     * Prepares keyword phrases form texts's words.
     *
     * @param array $words
     * @param int $maxPhraseWords
     * @return array
     */
    protected function getKeywords(array $words, int $maxPhraseWords): array
    {
        $count = count($words);
        $keywords = [];
        for ($i = 0; $i < $count; $i++) {
            for ($x = 1; $x <= $maxPhraseWords; $x++) {
                if ($i + $x <= $count) {
                    $phrase = [];
                    for ($y = 0; $y < $x; $y++) {
                        $phrase[] = $words[$i + $y];
                    }
                    $keywords[$x][] = implode(" ", $phrase);
                }
            }
        }
        return $keywords;
    }

    /**
     * Returns overused keywords based on max count specified.
     *
     * @param array $keywords
     * @param int $maxPercentage
     * @param int $maxPhraseWords
     * @return array
     */
    protected function getOverusedKeywords(array $keywords, int $maxPercentage = 10, int $maxPhraseWords = 4): array
    {
        $overusedWords = [];
        for ($i = 1; $i <= $maxPhraseWords; $i++) {
            if (!empty($keywords[$i])) {
                foreach ($keywords[$i] as $keyword => $percentage) {
                    $actualMaxPercentage = ($maxPercentage * $i);
                    if ($actualMaxPercentage > 100) {
                        $actualMaxPercentage = 100;
                    }
                    if ($percentage > $actualMaxPercentage) {
                        $overusedWords[] = $keyword;
                    }
                }
            }
        }
        return $overusedWords;
    }
}
