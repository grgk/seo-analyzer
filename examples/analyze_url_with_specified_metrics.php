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
$analyzer->metrics = [$metrics['content_ratio'], $metrics['keyword_density']];
$results = $analyzer->analyze();


print_r($results);
