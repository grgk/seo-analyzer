<?php
/**
 * URL analyze example
 *
 * Executes full seo analyze of web page on specified URL
 */

require_once(__DIR__ . '/../vendor/autoload.php');

use SeoAnalyzer\Page;
use SeoAnalyzer\Factor;
use SeoAnalyzer\Analyzer;
use SeoAnalyzer\HttpClient\Exception\HttpException;

try {
    $page = new Page('http://www.msn.com/pl-pl', 'pl_PL');
    $page->stopWords = ['nie', 'jak', 'msn', 'tak', 'jest', 'kiedy', 'tym'];
    $analyzer = new Analyzer($page);
    $analyzer->metrics = $page->setMetrics([[Factor::DENSITY_PAGE => 'keywordDensity']]);
    $results = $analyzer->analyze();
} catch (HttpException $e) {
    echo "Error loading page: " . $e->getMessage();
} catch (ReflectionException $e) {
    echo "Error loading metric file: " . $e->getMessage();
}

print_r($results);
