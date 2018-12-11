<?php
/**
 * Web page analyze example
 *
 * Executes web page full seo analyze.
 */

require_once(__DIR__ . '/../vendor/autoload.php');

use SeoAnalyzer\Analyzer;

try {
    $results = (new Analyzer())->analyzeUrl('https://www.msn.com/en-us', 'msn');
} catch (\SeoAnalyzer\HttpClient\Exception\HttpException $e) {
    // Can't open URL
}

print_r($results);
