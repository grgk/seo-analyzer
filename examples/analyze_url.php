<?php
/**
 * URL analyze example
 *
 * Executes full seo analyze of web page on specified URL
 */

require_once(__DIR__ . '/../vendor/autoload.php');

use SeoAnalyzer\Analyzer;

try {
    $results = (new Analyzer())->analyzeUrl('http://www.msn.com/en-us');
} catch (\SeoAnalyzer\HttpClient\Exception\HttpException $e) {
    // Can't open URL
}

print_r($results);
