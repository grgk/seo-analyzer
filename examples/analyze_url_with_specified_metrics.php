<?php
/**
 * URL analyze example
 *
 * Executes full seo analyze of web page on specified URL
 */

require_once(__DIR__ . '/../vendor/autoload.php');

use SeoAnalyzer\Analyzer;
use SeoAnalyzer\Page;

try {
    $page = new Page('http://www.msn.com/en-us');
} catch (\SeoAnalyzer\HttpClient\Exception\HttpException $e) {
    // Can't open URL
}
$analyzer = new Analyzer($page);
$metrics = $analyzer->getMetrics();
$analyzer->metrics = [$metrics['page_content_ratio'], $metrics['page_keywordDensity']];
$results = $analyzer->analyze();

print_r($results);
